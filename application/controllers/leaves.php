<?php
/**
 * This controller contains the actions allowing an employee to list and manage its leave requests
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */

if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

//We can define custom triggers before saving the leave request into the database
require_once FCPATH . "local/triggers/leave.php";

/**
 * This class allows an employee to list and manage its leave requests
 * Since 0.4.3 a trigger is called at the creation, if the function triggerCreateLeaveRequest is defined
 * see content of /local/triggers/leave.php
 */
class Leaves extends CI_Controller {
    
    /**
     * Default constructor
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function __construct() {
        parent::__construct();
        setUserContext($this);
        $this->load->model('leaves_model');
        $this->load->model('types_model');
        $this->lang->load('leaves', $this->language);
        $this->lang->load('global', $this->language);
    }
    
    /**
     * Display the list of the leave requests of the connected user
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function index() {
        $this->auth->checkIfOperationIsAllowed('list_leaves');
        $data = getUserContext($this);
        $this->lang->load('datatable', $this->language);
        $data['leaves'] = $this->leaves_model->getLeavesOfEmployee($this->session->userdata('id'));
        $data['title'] = lang('leaves_index_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_leave_requests_list');
        $data['flash_partial_view'] = $this->load->view('templates/flash', $data, TRUE);
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('leaves/index', $data);
        $this->load->view('templates/footer');
    }
    
    /**
     * Display the details of leaves taken/entitled for the connected user
     * @param string $refTmp Timestamp (reference date)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function counters($refTmp = NULL) {
        $this->auth->checkIfOperationIsAllowed('counters_leaves');
        $data = getUserContext($this);
        $this->lang->load('datatable', $this->language);
        $refDate = date("Y-m-d");
        if ($refTmp != NULL) {
            $refDate = date("Y-m-d", $refTmp);
            $data['isDefault'] = 0;
        } else {
            $data['isDefault'] = 1;
        }
        $data['refDate'] = $refDate;
        $data['summary'] = $this->leaves_model->getLeaveBalanceForEmployee($this->user_id, FALSE, $refDate);

        if (!is_null($data['summary'])) {
            $data['title'] = lang('leaves_summary_title');
            $data['help'] = $this->help->create_help_link('global_link_doc_page_my_summary');
            $this->load->view('templates/header', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('leaves/counters', $data);
            $this->load->view('templates/footer');
        } else {
            $this->session->set_flashdata('msg', lang('leaves_summary_flash_msg_error'));
            redirect('leaves');
        }
    }

    /**
     * Display a leave request
     * @param string $source Page source (leaves, requests) (self, manager)
     * @param int $id identifier of the leave request
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function view($source, $id) {
        $this->auth->checkIfOperationIsAllowed('view_leaves');
        $data = getUserContext($this);
        $data['leave'] = $this->leaves_model->getLeaves($id);
        if (empty($data['leave'])) {
            redirect('notfound');
        }
        //If the user is not its not HR, not manager and not the creator of the leave
        //the employee can't see it, redirect to LR list
        if ($data['leave']['employee'] != $this->user_id) {
            if ((!$this->is_hr)) {
                $this->load->model('users_model');
                $employee = $this->users_model->getUsers($data['leave']['employee']);
                if ($employee['manager'] != $this->user_id) {
                    $this->load->model('delegations_model');
                    if (!$this->delegations_model->isDelegateOfManager($this->user_id, $employee['manager'])) {
                        log_message('error', 'User #' . $this->user_id . ' illegally tried to view leave #' . $id);
                        redirect('leaves');
                    }
                }
            } //Admin
        } //Current employee
        $data['source'] = $source;
        $data['title'] = lang('leaves_view_html_title');
        if ($source == 'requests') {
            if (empty($employee)) {
                $this->load->model('users_model');
                $data['name'] = $this->users_model->getName($data['leave']['employee']);
            } else {
                $data['name'] = $employee['firstname'] . ' ' . $employee['lastname'];
            }
        } else {
            $data['name'] = '';
        }
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('leaves/view', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Create a leave request
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function create() {
        $this->auth->checkIfOperationIsAllowed('create_leaves');
        $data = getUserContext($this);
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['title'] = lang('leaves_create_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_request_leave');
        
        $this->form_validation->set_rules('startdate', lang('leaves_create_field_start'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('startdatetype', 'Start Date type', 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('enddate', lang('leaves_create_field_end'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('enddatetype', 'End Date type', 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('duration', lang('leaves_create_field_duration'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('type', lang('leaves_create_field_type'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('cause', lang('leaves_create_field_cause'), 'xss_clean|strip_tags');
        $this->form_validation->set_rules('status', lang('leaves_create_field_status'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('substitute', lang('leaves_create_field_substitute'), 'xss_clean|strip_tags');

        $data['credit'] = 0;
        $default_type = $this->config->item('default_leave_type');
        $default_type = $default_type == FALSE ? 1 : $default_type;
        if ($this->form_validation->run() === FALSE) {
            $data['types'] = $this->types_model->getTypes();
            foreach ($data['types'] as $type) {
                if ($type['id'] == $default_type) {
                    $data['credit'] = $this->leaves_model->getLeavesTypeBalanceForEmployee($this->user_id, $type['name']);
                    break;
                }
            }

            $data['substitute'] = $this->users_model->getEmployeesOfOrganization($this->user_id);

            $this->load->view('templates/header', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('leaves/create');
            $this->load->view('templates/footer');
        } else {
            if (($this->input->post('type') != 2)||($this->input->post('status') == 1)){
                if (function_exists('triggerCreateLeaveRequest')) {
                    triggerCreateLeaveRequest($this);
                }
                $leave_id = $this->leaves_model->setLeaves($this->session->userdata('id'));
                $this->session->set_flashdata('msg', lang('leaves_create_flash_msg_success'));
            }
            //If the status is requested, send an email
            if ($this->input->post('status') == 2) {

                //If the type is (2 : right to leave), send an email to the hr admin
                if ($this->input->post('type') == 2) {
                    foreach ($data['types'] as $type) {
                            if ($type['id'] == 1) {
                                $name_id = $type['name'];
                                break;
                            }
                    } 
                    if ($this->leaves_model->getLeavesTypeBalanceForEmployee($this->user_id, $name_id) <= 0) 
                    {//choice an hr admin
                        if (function_exists('triggerCreateLeaveRequest')) {
                            triggerCreateLeaveRequest($this);
                        }
                        $leave_id = $this->leaves_model->setLeaves($this->session->userdata('id'));
                        $this->session->set_flashdata('msg', lang('leaves_create_flash_msg_success'));

                        $this->load->model('users_model');
                        $hr_id = $this->users_model->getAdmins()[0]['id'];
                        $this->sendMailToRH($leave_id, $hr_id);
                    }
                 } 
                 else $this->sendMail($leave_id); // send an email to the manager
            }
            if (isset($_GET['source'])) {
                redirect($_GET['source']);
            } else {
                redirect('leaves');
            }
        }
    }
    
    /**
     * Edit a leave request
     * @param int $id Identifier of the leave request
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function edit($id) {
        $this->auth->checkIfOperationIsAllowed('edit_leaves');
        $data = getUserContext($this);
        $data['leave'] = $this->leaves_model->getLeaves($id);
        //Check if exists
        if (empty($data['leave'])) {
            redirect('notfound');
        }
        //If the user is not its own manager and if the leave is 
        //already requested, the employee can't modify it
        if (!$this->is_hr) {
            if (($this->session->userdata('manager') != $this->user_id) &&
                    $data['leave']['status'] != 1) {
                if ($this->config->item('edit_rejected_requests') == FALSE ||
                    $data['leave']['status'] != 4) {//Configuration switch that allows editing the rejected leave requests
                    log_message('error', 'User #' . $this->user_id . ' illegally tried to edit leave #' . $id);
                    $this->session->set_flashdata('msg', lang('leaves_edit_flash_msg_error'));
                    redirect('leaves');
                 }
            }
        } //Admin
        
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['title'] = lang('leaves_edit_html_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_request_leave');
        $data['id'] = $id;
        
        $data['credit'] = 0;
        $data['types'] = $this->types_model->getTypes();
        foreach ($data['types'] as $type) {
            if ($type['id'] == $data['leave']['type']) {
                $data['credit'] = $this->leaves_model->getLeavesTypeBalanceForEmployee($data['leave']['employee'], $type['name']);
                break;
            }
        }
        
        $data['substitute'] = $this->users_model->getEmployeesOfOrganization($data['leave']['employee']);
        
        $this->form_validation->set_rules('startdate', lang('leaves_edit_field_start'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('startdatetype', 'Start Date type', 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('enddate', lang('leaves_edit_field_end'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('enddatetype', 'End Date type', 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('duration', lang('leaves_edit_field_duration'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('type', lang('leaves_edit_field_type'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('cause', lang('leaves_edit_field_cause'), 'xss_clean|strip_tags');
        $this->form_validation->set_rules('status', lang('leaves_edit_field_status'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('substitute', lang('leaves_edit_field_substitute'), 'xss_clean|strip_tags');

        if ($this->form_validation->run() === FALSE) {
            $this->load->model('users_model');
            $data['name'] = $this->users_model->getName($data['leave']['employee']);
            $this->load->view('templates/header', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('leaves/edit', $data);
            $this->load->view('templates/footer');
        } else {
            if (($this->input->post('type') != 2)||($this->input->post('status') == 1)){
                $this->leaves_model->updateLeaves($id);       //We don't use the return value
                $this->session->set_flashdata('msg', lang('leaves_edit_flash_msg_success'));
            }
            //If the status is requested, send an email
            if ($this->input->post('status') == 2) {
                
                ///If the type is (2 : right to leave), send an email to the hr admin
                if ($this->input->post('type') == 2) {
                    foreach ($data['types'] as $type) {
                            if ($type['id'] == 1) {
                                $name_id = $type['name'];
                                break;
                            }
                    } 
                    if ($this->leaves_model->getLeavesTypeBalanceForEmployee($this->user_id, $name_id) <= 0) 
                    {//choice an hr admin
                        $this->leaves_model->updateLeaves($id);       //We don't use the return value
                        $this->session->set_flashdata('msg', lang('leaves_edit_flash_msg_success'));

                        $this->load->model('users_model');
                        $hr_id = $this->users_model->getAdmins()[0]['id'];
                        $this->sendMailToRH($id, $hr_id);
                    }
                 } 
                 else $this->sendMail($id); // send an email to the manager
            }
            if (isset($_GET['source'])) {
                redirect($_GET['source']);
            } else {
                redirect('leaves');
            }
        }
    }
    
    /**
     * Send a leave request email to the manager of the connected employee
     * @param int $id Leave request identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function sendMail($id) {
        $this->load->model('users_model');
        $this->load->model('types_model');
        $this->load->model('delegations_model');
        //We load everything from DB as the LR can be edited from HR/Employees
        $leave = $this->leaves_model->getLeaves($id);
        $user = $this->users_model->getUsers($leave['employee']);
        $manager = $this->users_model->getUsers($user['manager']);
        $substitute = $this->users_model->getUsers($leave['substitute']);

        //Test if the manager or the substitute haven't been deleted meanwhile
        if (empty($substitute)) {
            $this->session->set_flashdata('msg', lang('leaves_create_flash_msg_error_substitute'));
        }
        if (empty($manager['email'])) {
            $this->session->set_flashdata('msg', lang('leaves_create_flash_msg_error'));
        } else {
            //Send an e-mail to the manager
            $this->load->library('email');
            $this->load->library('polyglot');
            $usr_lang = $this->polyglot->code2language($manager['language']);

            //We need to instance an different object as the languages of connected user may differ from the UI lang
            $lang_mail = new CI_Lang();
            $lang_mail->load('email', $usr_lang);
            $lang_mail->load('global', $usr_lang);
            
            $date = new DateTime($leave['startdate']);
            $startdate = $date->format($lang_mail->line('global_date_format'));
            $date = new DateTime($leave['enddate']);
            $enddate = $date->format($lang_mail->line('global_date_format'));

            $this->load->library('parser');
            $data = array(
                'Title' => $lang_mail->line('email_leave_request_title'),
                'Firstname' => $user['firstname'],
                'Lastname' => $user['lastname'],
                'StartDate' => $startdate,
                'EndDate' => $enddate,
                'StartDateType' => $lang_mail->line($leave['startdatetype']),
                'EndDateType' => $lang_mail->line($leave['enddatetype']),
                'Type' => $this->types_model->getName($leave['type']),
                'Duration' => $leave['duration'],
                'Balance' => $this->leaves_model->getLeavesTypeBalanceForEmployee($leave['employee'] , $leave['type_name'], $leave['startdate']),
                'Reason' => $leave['cause'],
                'FirstnameSubstitute' => $substitute['firstname'],
                'LastnameSubstitute' => $substitute['lastname'],
                'BaseUrl' => $this->config->base_url(),
                'LeaveId' => $id,
                'UserId' => $this->user_id,
                'SubstituteId' => $substitute['id']
            );
            $message = $this->parser->parse('emails/' . $manager['language'] . '/request', $data, TRUE);
            $this->email->set_encoding('quoted-printable');
            
            if ($this->config->item('from_mail') != FALSE && $this->config->item('from_name') != FALSE ) {
                $this->email->from($this->config->item('from_mail'), $this->config->item('from_name'));
            } else {
               $this->email->from('do.not@reply.me', 'LMS');
            }
            $this->email->to($manager['email']);
            if ($this->config->item('subject_prefix') != FALSE) {
                $subject = $this->config->item('subject_prefix');
            } else {
               $subject = '[Baraka] ';
            }
            //Copy to the delegates, if any
            $delegates = $this->delegations_model->listMailsOfDelegates($manager['id']);
            if ($delegates != '') {
                $this->email->cc($delegates);
            }
            //Copy to the substitute, if any
            /*if ($substitute['email'] != '') {
                $this->email->cc($substitute['email']);
            }*/
            $this->email->subject($subject . $lang_mail->line('email_leave_request_subject') . ' ' .
                    $this->session->userdata('firstname') . ' ' .
                    $this->session->userdata('lastname'));
            $this->email->message($message);
            $this->email->send();
        }
    }
    /**
     * Send a leave request email to the RH
     * @param int $id Leave request identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    private function sendMailToRH($id, $id_hr) {
        $this->load->model('users_model');
        $this->load->model('types_model');
        //We load everything from DB as the LR can be edited from HR/Employees
        $leave = $this->leaves_model->getLeaves($id);
        $user = $this->users_model->getUsers($leave['employee']);
        $manager = $this->users_model->getUsers($user['manager']);
        //$substitute = $this->users_model->getUsers($leave['substitute']);
        $hr_admin = $this->users_model->getUsers($id_hr);

        //Test if the hr admin haven't been deleted meanwhile
        if (empty($hr_admin)) {
            $this->session->set_flashdata('msg', lang('leaves_create_flash_msg_error_admin'));
        } else {
            //Send an e-mail to the hr admin
            $this->load->library('email');
            $this->load->library('polyglot');
            $usr_lang = $this->polyglot->code2language($hr_admin['language']);
            
            //We need to instance an different object as the languages of connected user may differ from the UI lang
            $lang_mail = new CI_Lang();
            $lang_mail->load('email', $usr_lang);
            $lang_mail->load('global', $usr_lang);
            
            $date = new DateTime($leave['startdate']);
            $startdate = $date->format($lang_mail->line('global_date_format'));
            $date = new DateTime($leave['enddate']);
            $enddate = $date->format($lang_mail->line('global_date_format'));

            $this->load->library('parser');
            $data = array(
                'Title' => $lang_mail->line('email_leave_request_title'),
                'Firstname' => $user['firstname'],
                'Lastname' => $user['lastname'],
                'StartDate' => $startdate,
                'EndDate' => $enddate,
                'StartDateType' => $lang_mail->line($leave['startdatetype']),
                'EndDateType' => $lang_mail->line($leave['enddatetype']),
                'Type' => $this->types_model->getName($leave['type']),
                'Duration' => $leave['duration'],
                'Balance' => $this->leaves_model->getLeavesTypeBalanceForEmployee($leave['employee'] , $leave['type_name'], $leave['startdate']),
                'Reason' => $leave['cause'],
                'FirstnameManager' => $manager['firstname'],
                'LastnameManager' => $manager['lastname'],
                'BaseUrl' => $this->config->base_url(),
                'LeaveId' => $id,
                'UserId' => $this->user_id,
                'ManagerId' => $manager['id']
            );
            $message = $this->parser->parse('emails/' . $hr_admin['language'] . '/request_hr', $data, TRUE);
            $this->email->set_encoding('quoted-printable');
            
            if ($this->config->item('from_mail') != FALSE && $this->config->item('from_name') != FALSE ) {
                $this->email->from($this->config->item('from_mail'), $this->config->item('from_name'));
            } else {
               $this->email->from('do.not@reply.me', 'LMS');
            }
            $this->email->to($hr_admin['email']);
            if ($this->config->item('subject_prefix') != FALSE) {
                $subject = $this->config->item('subject_prefix');
            } else {
               $subject = '[Baraka] ';
            }
            
            $this->email->subject($subject . $lang_mail->line('email_leave_request_subject') . ' ' .
                    $this->session->userdata('firstname') . ' ' .
                    $this->session->userdata('lastname'));
            $this->email->message($message);
            $this->email->send();
        }
    }

    /**
     * Delete a leave request
     * @param int $id identifier of the leave request
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function delete($id) {
        $can_delete = FALSE;
        //Test if the leave request exists
        $leaves = $this->leaves_model->getLeaves($id);
        if (empty($leaves)) {
            redirect('notfound');
        } else {
            if ($this->is_hr) {
                $can_delete = TRUE;
            } else {
                if ($leaves['status'] == 1 ) {
                    $can_delete = TRUE;
                }
                if ($this->config->item('delete_rejected_requests') == TRUE ||
                    $leaves['status'] == 4) {
                    $can_delete = TRUE;
                }
            }
            if ($can_delete === TRUE) {
                $this->leaves_model->deleteLeave($id);
            } else {
                $this->session->set_flashdata('msg', lang('leaves_delete_flash_msg_error'));
                if (isset($_GET['source'])) {
                    redirect($_GET['source']);
                } else {
                    redirect('leaves');
                }
            }
        }
        $this->session->set_flashdata('msg', lang('leaves_delete_flash_msg_success'));
        if (isset($_GET['source'])) {
            redirect($_GET['source']);
        } else {
            redirect('leaves');
        }
    }

    /**
     * Export the list of all leaves into an Excel file
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function export() {
        $this->load->library('excel');
        $this->load->view('leaves/export');
    }

    /**
     * Ajax endpoint : Send a list of fullcalendar events
     * @param int $id employee id or connected user (from session)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function individual($id = 0) {
        header("Content-Type: application/json");
        $start = $this->input->get('start', TRUE);
        $end = $this->input->get('end', TRUE);
        if ($id == 0) $id =$this->session->userdata('id');
        echo $this->leaves_model->individual($id, $start, $end);
    }

    /**
     * Ajax endpoint : Send a list of fullcalendar events
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function workmates() {
        header("Content-Type: application/json");
        $start = $this->input->get('start', TRUE);
        $end = $this->input->get('end', TRUE);
        echo $this->leaves_model->workmates($this->session->userdata('manager'), $start, $end);
    }
    
    /**
     * Ajax endpoint : Send a list of fullcalendar events
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function collaborators() {
        header("Content-Type: application/json");
        $start = $this->input->get('start', TRUE);
        $end = $this->input->get('end', TRUE);
        echo $this->leaves_model->collaborators($this->user_id, $start, $end);
    }

    /**
     * Ajax endpoint : Send a list of fullcalendar events
     * @param int $entity_id Entity identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function organization($entity_id) {
        header("Content-Type: application/json");
        $start = $this->input->get('start', TRUE);
        $end = $this->input->get('end', TRUE);
        $children = filter_var($this->input->get('children', TRUE), FILTER_VALIDATE_BOOLEAN);
        echo $this->leaves_model->department($entity_id, $start, $end, $children);
    }

    /**
     * Ajax endpoint : Send a list of fullcalendar events
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function department() {
        header("Content-Type: application/json");
        $this->load->model('organization_model');
        $department = $this->organization_model->getDepartment($this->user_id);
        $start = $this->input->get('start', TRUE);
        $end = $this->input->get('end', TRUE);
        echo $this->leaves_model->department($department[0]['id'], $start, $end);
    }
    
    /**
     * Ajax endpoint. Result varies according to input :
     *  - difference between the entitled and the taken days
     *  - try to calculate the duration of the leave
     *  - try to detect overlapping leave requests
     *  If the user is linked to a contract, returns end date of the yearly leave period or NULL
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    /**
     * Calculate the actual length of a leave request without taking into account the non-working days
     * Detect overlapping with non-working days. It returns a K/V arrays of 3 items.
     * @param int $employee Identifier of the employee
     * @param date $startdate start date of the leave request
     * @param date $enddate end date of the leave request
     * @param string $startdatetype start date type of leave request being created (Morning or Afternoon)
     * @param string $enddatetype end date type of leave request being created (Morning or Afternoon)
     * @param array List of non-working days
     * @return array (length=>length of leave, overlapping=>excat match with a non-working day, daysoff=>sum of days off)
     * @author Belkaid Aissa <da_belkaid@esi.dz>
     */
        public function actualLengthWithoutDaysOff($employee, $startdate, $enddate, $startdatetype, $enddatetype, $daysoff) {
        $startDateObject = DateTime::createFromFormat('Y-m-d H:i:s', $startdate . ' 00:00:00');
        $endDateObject = DateTime::createFromFormat('Y-m-d H:i:s', $enddate . ' 00:00:00');
        $iDate = clone $startDateObject;

        //Simplify logic
        if ($startdate == $enddate) $one_day = TRUE; else $one_day = FALSE;
        if ($startdatetype == 'Morning') $start_morning = TRUE; else $start_morning = FALSE;
        if ($startdatetype == 'Afternoon') $start_afternoon = TRUE; else $start_afternoon = FALSE;
        if ($enddatetype == 'Morning') $end_morning = TRUE; else $end_morning = FALSE;
        if ($enddatetype == 'Afternoon') $end_afternoon = TRUE; else $end_afternoon = FALSE;

        //Iteration between start and end dates of the leave request
        $lengthDaysOff = 0;
        $length = 0;
        $hasDayOff = FALSE;
        $overlapDayOff = FALSE;
        while ($iDate <= $endDateObject)
        {
            if ($iDate == $startDateObject) $first_day = TRUE; else $first_day = FALSE;
            $isDayOff = FALSE;
            //Iterate on the list of days off with two objectives:
            // - Compute sum of days off between the two dates
            // - Detect if the leave request exactly overlaps with a day off
            foreach ($daysoff as $dayOff) {
                $dayOffObject = DateTime::createFromFormat('Y-m-d H:i:s', $dayOff['date'] . ' 00:00:00');
                if ($dayOffObject == $iDate) {
                    $lengthDaysOff+=$dayOff['length'];
                    $hasDayOff = TRUE;
                    switch ($dayOff['type']) {
                        case 1: //1 : All day
                            if ($one_day && $start_morning && $end_afternoon && $first_day)
                                $overlapDayOff = TRUE;
                            break;
                        case 2: //2 : Morning
                            if ($one_day && $start_morning && $end_morning && $first_day)
                                $overlapDayOff = TRUE;
                            else
                                $length+=0.5;
                            break;
                        case 3: //3 : Afternnon
                            if ($one_day && $start_afternoon && $end_afternoon && $first_day)
                                $overlapDayOff = TRUE;
                            else
                                $length+=0.5;
                            break;
                        default:
                            break;
                    }
                    break;
                }
            }
            if (!$isDayOff) {
                if ($one_day) {
                    if ($start_morning && $end_afternoon) $length++;
                    if ($start_morning && $end_morning) $length+=0.5;
                    if ($start_afternoon && $end_afternoon) $length+=0.5;
                } else {
                    if ($iDate == $endDateObject) $last_day = TRUE; else $last_day = FALSE;
                    if (!$first_day && !$last_day) $length++;
                    if ($first_day && $start_morning) $length++;
                    if ($first_day && $start_afternoon) $length+=0.5;
                    if ($last_day && $end_morning) $length+=0.5;
                    if ($last_day && $end_afternoon) $length++;
                }
                $overlapDayOff = FALSE;
            }
            $iDate->modify('+1 day');   //Next day
        }

        //Other obvious cases of overlapping
        if ($hasDayOff && ($length == 0)) {
            $overlapDayOff = TRUE;
        }
        return array('length' => $length, 'daysoff' => $lengthDaysOff, 'overlapping' => $overlapDayOff);
    }public function validate() {
        header("Content-Type: application/json");
        $id = $this->input->post('id', TRUE);
        $type = $this->input->post('type', TRUE);
        $startdate = $this->input->post('startdate', TRUE);
        $enddate = $this->input->post('enddate', TRUE);
        $startdatetype = $this->input->post('startdatetype', TRUE);     //Mandatory field checked by frontend
        $enddatetype = $this->input->post('enddatetype', TRUE);       //Mandatory field checked by frontend
        $leave_id = $this->input->post('leave_id', TRUE);
        $leaveValidator = new stdClass;
        $data = getUserContext($this);
        $data['types'] = $this->types_model->getTypes();
        foreach ($data['types'] as $type_1) {
            if ($type_1['name'] == $type) {
                $variable = $type_1['id'];
                break; 
            }
        }
        if (isset($id) && isset($type)) {
            if (isset($startdate) && $startdate !== "") {
                if ($variable!=1 && $variable!=2) $leaveValidator->credit = '';
                else $leaveValidator->credit = $this->leaves_model->getLeavesTypeBalanceForEmployee($id, $type, $startdate);
            } else {
                if ($variable!=1 && $variable!=2) $leaveValidator->credit = '';
                else $leaveValidator->credit = $this->leaves_model->getLeavesTypeBalanceForEmployee($id, $type, $startdate);
            }
        }
        if (isset($id) && isset($startdate) && isset($enddate)) {
            if (isset($leave_id)) {
                $leaveValidator->overlap = $this->leaves_model->detectOverlappingLeaves($id, $startdate, $enddate, $startdatetype, $enddatetype, $leave_id);
            } else {
                $leaveValidator->overlap = $this->leaves_model->detectOverlappingLeaves($id, $startdate, $enddate, $startdatetype, $enddatetype);
            }
        }
        
        //Returns end date of the yearly leave period or NULL if the user is not linked to a contract
        $this->load->model('contracts_model');
        $startentdate = NULL;
        $endentdate = NULL;
        $hasContract = $this->contracts_model->getBoundaries($id, $startentdate, $endentdate);
        $leaveValidator->PeriodStartDate = $startentdate;
        $leaveValidator->PeriodEndDate = $endentdate;
        $leaveValidator->hasContract = $hasContract;
        
        //Add non working days between the two dates (including their type: morning, afternoon and all day)
        if (isset($id) && ($startdate!='') && ($enddate!='')  && $hasContract===TRUE) {
            $this->load->model('dayoffs_model');
            $leaveValidator->listDaysOff = $this->dayoffs_model->listOfDaysOffBetweenDates($id, $startdate, $enddate);
            //Sum non-working days and overlapping with day off detection
            if ($variable==1 || $variable==2) $result = $this->leaves_model->actualLengthWithoutDaysOff($id, $startdate, $enddate, $startdatetype, $enddatetype, $leaveValidator->listDaysOff);
            else $result = $this->leaves_model->actualLengthAndDaysOff($id, $startdate, $enddate, $startdatetype, $enddatetype, $leaveValidator->listDaysOff);
            $leaveValidator->overlapDayOff = $result['overlapping'];
            $leaveValidator->lengthDaysOff = $result['daysoff'];
            $leaveValidator->length = $result['length'];
        }
        //If the user has no contract, simply compute a date difference between start and end dates
        if (isset($id) && isset($startdate) && isset($enddate)  && $hasContract===FALSE) {
            $leaveValidator->length = $this->leaves_model->length($id, $startdate, $enddate, $startdatetype, $enddatetype);
        }
        
        //Repeat start and end dates of the leave request
        $leaveValidator->RequestStartDate = $startdate;
        $leaveValidator->RequestEndDate = $enddate;
        echo json_encode($leaveValidator);
    }
}
