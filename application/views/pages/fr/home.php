<div class="row-fluid">
    <div class="page-title">
        <h1>Gestion des Demandes de Congé</h1>
        <p>Bienvenue dans l'application de gestion des demandes de congé.</p>
    </div>
    <div class="table-responsive">
        <table cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-striped table-hover" id="contracts" width="100%">
            <thead id="table-titles">
                <tr>    
                    <th>Les employés</th>
                    <?php if ($is_manager == TRUE) { ?><th>Les responsables</th><?php } ?>
                    <?php if ($is_hr == TRUE) { ?><th>Les administrateurs RH</th><?php } ?>
                </tr>
            </thead>
            <tbody>
            <tr>
                <td>Soumettre une <a href="<?php echo base_url();?>leaves/create">nouvelle demande</a></td>
                <?php if ($is_manager == TRUE) { ?><td>Valider les <a href="<?php echo base_url();?>requests">demandes de congés</a> qui vous ont été soumises</td><?php } ?>
                <?php if ($is_hr == TRUE) { ?><td>Créer, modifier, filter or supprimer <a href="<?php echo base_url();?>users">un utilisateur</a></td><?php } ?>
            </tr>
            <tr>
                <td>Voir la <a href="<?php echo base_url();?>leaves">liste des demandes de congés</a> que vous avez soumises</td>
                <?php if ($is_manager == TRUE) { ?><td>Lister vos <a href="<?php echo base_url();?>requests/collaborators">subordonnés</a> pour contrôler la présence</td><?php } ?>
                <?php if ($is_hr == TRUE) { ?><td>Attacher le responsable et les employés <a href="<?php echo base_url();?>organization">à un service donné</td><?php } ?>
            </tr>
            <tr>
                <td>Voir vos <a href="<?php echo base_url();?>leaves/counters">compteurs de congés</a></td>
                <?php if ($is_manager == TRUE) { ?><td>Choisir les <a href="<?php echo base_url();?>requests/delegations">délégués</a> qui peuvent recevoir les demandes à votre place</a></td><?php } ?>
                <?php if ($is_hr == TRUE) { ?><td>Suivre <a href="<?php echo base_url();?>hr/employees">l'état des congés</a> (titre de congé, contrat, rapport...)</td><?php } ?>
            </tr>
            <tr>
                <td>Visualiser vos <a href="<?php echo base_url();?>calendar/individual">calendriers (individuel et annuel)</td>
                <?php if ($is_manager == TRUE) { ?><td>Visualiser le <a href="<?php echo base_url();?>calendar/collaborators">calendrier de vos subordonnés</td><?php } ?>
                <?php if ($is_hr == TRUE) { ?><td>Visualiser le <a href="<?php echo base_url();?>calendar/organization">calendrier global de l'organisation</a></td><?php } ?>
            </tr>
            </tbody>
        </table>
    </div>
</div>