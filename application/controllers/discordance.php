<?php
/**
 * This controller allows to manage the contracts
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */

if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

/**
 * This class allows to manage the contracts. Each employee has a contract.
 * On a contract, you can define:
 *  - The non-working days (week-ends, public days off, part-time, etc.).
 *  - Entitled days for all employees attached to this contract.
 *  - The default period for leave credit (taken, available, entitled).
 */
class Discordance extends CI_Controller {
    
    /**
     * Default constructor
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function __construct() {
        parent::__construct();
        setUserContext($this);
        $this->lang->load('discordance', $this->language);
        $this->load->model('discordance_model');
    }
    
    /**
     * Display the list of all contracts defined in the system
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function index() {
        $this->auth->checkIfOperationIsAllowed('list_contracts');
        $this->lang->load('datatable', $this->language);
        $data = getUserContext($this);
        $data['title'] = lang('discordance_index_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_contracts_list');
        $data['discordances'] = $this->discordance_model->getContracts();
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('discordance/index', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Display the list of all contracts defined in the system
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function pay() {
        $this->auth->checkIfOperationIsAllowed('list_contracts');
        $this->lang->load('datatable', $this->language);
        $data = getUserContext($this);
        $data['title'] = lang('discordance_pay_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_contracts_list');
        $data['discordances'] = $this->discordance_model->getPays();
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('discordance/pay', $data);
        $this->load->view('templates/footer');
    }
    
    /**
     * Display a form that allows to update a contract
     * @param int $id Contract identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function edit($disponible, $available, $identitled) {
        //$this->auth->checkIfOperationIsAllowed('list_contracts');
        //$data = getUserContext($this);
        $data = $this->db->query("UPDATE entitleddays SET days = (days + ".$available." - ".$disponible.") WHERE Id = ".$identitled);
       // $data['entitleddays'] = $this->discordance_model->getPays($id);
       /* if (empty($data['entitleddays'])) {
            redirect('notfound');
        } */
            $this->load->view('templates/header', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('discordance/pay', $data);
            $this->load->view('templates/footer');
           // $this->discordance_model->updateContract();
            $this->session->set_flashdata('msg', lang('discordance_edit_msg_success'));
            redirect('discordance/pay');
    }
    
    /**
     * Display the form / action Create a new contract
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function create() {
        $this->auth->checkIfOperationIsAllowed('create_contract');
        $data = getUserContext($this);
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->lang->load('calendar', $this->language);
        $data['title'] = lang('discordance_create_title');

        $this->form_validation->set_rules('name', lang('discordance_create_field_name'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('startentdatemonth', lang('discordance_create_field_start_month'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('startentdateday', lang('discordance_create_field_start_day'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('endentdatemonth', lang('discordance_create_field_end_month'), 'required|xss_clean|strip_tags');
        $this->form_validation->set_rules('endentdateday', lang('discordance_create_field_end_day'), 'required|xss_clean|strip_tags');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('discordance/create', $data);
            $this->load->view('templates/footer');
        } else {
            $this->discordance_model->setContracts();
            $this->session->set_flashdata('msg', lang('discordance_create_msg_success'));
            redirect('discordance');
        }
    }
    
    /**
     * Delete a given contract
     * @param int $id contract identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function delete($id) {
        $this->auth->checkIfOperationIsAllowed('delete_contract');
        //Test if the contract exists
        $data['contract'] = $this->discordance_model->getContracts($id);
        if (empty($data['contract'])) {
            redirect('notfound');
        } else {
            $this->discordance_model->deleteContract($id);
        }
        $this->session->set_flashdata('msg', lang('discordance_delete_msg_success'));
        redirect('discordance');
    }
    
    /**
     * Display an interactive calendar that allows to dynamically set the days
     * off, bank holidays, etc. for a given contract
     * @param int $id contract identifier
     * @param int $year optional year number (4 digits), current year if empty
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function calendar($id, $year = 0) {
        $this->auth->checkIfOperationIsAllowed('calendar_contract');
        $data = getUserContext($this);
        $this->lang->load('calendar', $this->language);
        $data['title'] = lang('discordance_calendar_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_contracts_calendar');
        if ($year <> 0) {
            $data['year'] = $year;
        } else {
            $data['year'] = date("Y");
        }
        
        //Load the list of contracts (select destination contract / copy dayoff feature)
        $data['contracts'] = $this->discordance_model->getContracts();
        //Remove the contract being displayed (source)
        foreach ($data['contracts'] as $key => $value) {
            if ($value['id'] == $id) {
                unset($data['contracts'][$key]);
                break;
            }
        }
        $contract = $this->discordance_model->getContracts($id);
        $data['discordance_id'] = $id;
        $data['discordance_name'] = $contract['name'];
        $data['discordance_start_month'] = intval(substr($contract['startentdate'], 0, 2));
        $data['discordance_start_day'] = intval(substr($contract['startentdate'], 3));
        $data['discordance_end_month'] = intval(substr($contract['endentdate'], 0, 2));
        $data['discordance_end_day'] = intval(substr($contract['endentdate'], 3));
        $this->load->model('dayoffs_model');
        $data['dayoffs'] = $this->dayoffs_model->getDaysOffForCivilYear($id, $data['year']);
        $data['flash_partial_view'] = $this->load->view('templates/flash', $data, TRUE);
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('discordance/calendar', $data);
        $this->load->view('templates/footer');
    }
    
    /**
     * Copy the days off defined on a souce contract to another contract
     * for the civil year being displayed
     * @param int $source source contract identifier
     * @param int $destination destination contract identifier
     * @param int $year year number (4 digits)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function copydayoff($source, $destination, $year) {
        $this->auth->checkIfOperationIsAllowed('calendar_contract');
        $this->load->model('dayoffs_model');
        $this->dayoffs_model->copyListOfDaysOff($source, $destination, $year);
        //Redirect to the contract where we've just copied the days off
        $this->session->set_flashdata('msg', lang('discordance_calendar_copy_msg_success'));
        redirect('discordance/' . $destination . '/calendar/' . $year);
    }

    /**
     * Ajax endpoint : add a day off to a contract
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function editdayoff() {
        if ($this->auth->isAllowed('adddayoff_contract') === FALSE) {
            $this->output->set_header("HTTP/1.1 403 Forbidden");
        } else {
            $contract = $this->input->post('contract', TRUE);
            $timestamp = $this->input->post('timestamp', TRUE);
            $type = $this->input->post('type', TRUE);
            $title = sanitize($this->input->post('title', TRUE));
            if (isset($contract) && isset($timestamp) && isset($type) && isset($title)) {
                $this->load->model('dayoffs_model');
                $this->output->set_content_type('text/plain');
                if ($type == 0) {
                    echo $this->dayoffs_model->deleteDayOff($contract, $timestamp);
                } else {
                    echo $this->dayoffs_model->addDayOff($contract, $timestamp, $type, $title);
                }
            } else {
                $this->output->set_header("HTTP/1.1 422 Unprocessable entity");
            }
        }
    }
    
    /**
     * Ajax endpoint : Edit a series of day offs for a given contract
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function series() {
        if ($this->auth->isAllowed('adddayoff_contract') === FALSE) {
            $this->output->set_header("HTTP/1.1 403 Forbidden");
        } else {
            if (($this->input->post('day', TRUE) != NULL) && ($this->input->post('type', TRUE) != NULL) &&
                    ($this->input->post('start', TRUE) != NULL) && ($this->input->post('end', TRUE) != NULL)
                     && ($this->input->post('contract', TRUE) != NULL)) {
                header("Content-Type: text/plain");

                //Build the list of dates to be marked
                $start = strtotime($this->input->post('start', TRUE));
                $end = strtotime($this->input->post('end', TRUE));
                $type = $this->input->post('type', TRUE);
                $freq = $this->input->post('day', TRUE);
                if ($freq == "all") {
                    $day = $start;
                } else {
                    $day = strtotime($freq, $start);
                }
                
                $list = '';
                while ($day <= $end) {
                    $list .= date("Y-m-d", $day) . ",";
                    if ($freq == "all") {
                        $day = strtotime("+1 day", $day);
                    } else {
                        $day = strtotime("+1 weeks", $day);
                    }
                }
                $list = rtrim($list, ",");
                $contract = $this->input->post('contract', TRUE);
                $title = sanitize($this->input->post('title', TRUE));
                $this->load->model('dayoffs_model');
                $this->dayoffs_model->deleteListOfDaysOff($contract, $list);
                if ($type != 0) {
                    $this->dayoffs_model->addListOfDaysOff($contract, $type, $title, $list);
                    echo 'updated';
                } else {
                    echo 'deleted';
                }
            } else {
                $this->output->set_header("HTTP/1.1 422 Unprocessable entity");
            }
        }
    }

    /**
     * Ajax endpoint : Import non-working days by using an external ICS feed
     * This is an experimental feature that doesn't work with half days
     * POST: contract id
     * POST: URL of ICS feed
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function import() {
        header("Content-Type: plain/text");
        $contract = $this->input->post('contract', TRUE);
        $url = $this->input->post('url', TRUE);
        //Check validity of URL and if the endpoint is reachable
        if (!filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
            $headers = @get_headers($url);
            if(strpos($headers[0],'200') === FALSE) { //Anything else than HTTP 200 OK
                echo("$url was not found or distant server is not reachable");
            }
            else {
                $this->load->model('dayoffs_model');
                $this->dayoffs_model->importDaysOffFromICS($contract, $url);
            }
        } else {
            echo("$url is not a valid URL");
        }
    }
    
    /**
     * Ajax endpoint : Send a list of fullcalendar events
     * List of day offs for the connected user
     * @param int $id employee id or connected user (from session)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function userDayoffs($id = 0) {
        header("Content-Type: application/json");
        $start = $this->input->get('start', TRUE);
        $end = $this->input->get('end', TRUE);
        $this->load->model('dayoffs_model');
        if ($id == 0) $id =$this->user_id;
        echo $this->dayoffs_model->userDayoffs($id, $start, $end);
    }
    
    /**
     * Ajax endpoint : Send a list of fullcalendar events
     * List of all possible day offs
     * @param int $entity_id Entity identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function allDayoffs() {
        header("Content-Type: application/json");
        $start = $this->input->get('start', TRUE);
        $end = $this->input->get('end', TRUE);
        $entity = $this->input->get('entity', TRUE);
        $children = filter_var($this->input->get('children', TRUE), FILTER_VALIDATE_BOOLEAN);
        $this->load->model('dayoffs_model');
        echo $this->dayoffs_model->allDayoffs($start, $end, $entity, $children);
    }
    
    /**
     * Action: export the list of all contracts into an Excel file
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function export() {
        $this->auth->checkIfOperationIsAllowed('export_contracts');
        $this->load->library('excel');
        $this->load->view('discordance/export');
    }
}
