<?php
global $current_user;
get_currentuserinfo();


$umeta = get_user_meta($current_user->ID);

//print_r($umeta);


?>
<h2>Connect your contest to mailing list.</h2>

<div class="panel panel-default">
    <div class="panel-heading"><i class="fa fa-edit fa-fw"></i> MailChimp</div>
    <div class="panel-body">
        <div class="form-group">
            <label class="control-label" for="mcApiKey" style="display:block;">Enter your API KEY</label>
            <input id="mcApiKey" value="<?= get_user_meta($current_user->ID, '_mailchimApi_api', true) ?>" name=""
                   type="text" style="width:400px;position:relative; margin-right:6px; display:inline-block"
                   class="form-control input-md">
            <button style="position:relative; display:inline-block" id="mcapiverify"
                    class="btn btn-default"><?= (get_user_meta($current_user->ID, '_mailchimApi_api', true) != "") ? "Verified" : "Verify" ?></button>
        </div>
        <div class="mclistIds" id="mclistIds">
            <?php
            if (get_user_meta($current_user->ID, '_mailchimApi_api', true) != "") {
                $lists = get_user_meta($current_user->ID, '_mailchimApi_list', true);
//print_r($lists);

                ?>
                You have <?= count($lists) ?> lists to your MailChimp account.
                <div style="padding:10px 0px;" id="mailchimprest">
                    <table class="table table-bordered" style="width:320px !important">
                        <thead>
                        <tr>
                            <th>List Id</th>
                            <th>List Name</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (count($lists) > 1) {
                            $h = 0;
                            foreach ($lists as $lst_key => $lst_val) {

                                echo '<tr>';
                                echo '<td><input type="hidden" name="mc_list[' . $h . ']" value="' . $lst_val['list_id'] . '">' . $lst_val['list_id'] . '</td>';
                                echo '<td><input type="hidden" name="mc_name[' . $h . ']" value="' . $lst_val['list_name'] . '">' . $lst_val['list_name'] . '</td>';
                                echo '</tr>';
                                $h++;
                            }

                        } else {
                            echo '<tr>';
                            echo '<td><input type="hidden" name="mc_list[0]" value="' . $lists['lists'][0]['id'] . '">' . $lists['lists'][0]['id'] . '</td>';
                            echo '<td><input type="hidden" name="mc_name[0]" value="' . $lists['lists'][0]['name'] . '">' . $lists['lists'][0]['name'] . '</td>';
                            echo '</tr>';
                        }
                        ?>
                        </tbody>
                    </table>
                    <div style="padding:15px 0px 5px">

                        <button class="btn btn-info" id="mailchimp_save">Save</button>
                        <button class="btn btn-defualt">Cancel</button>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading"><i class="fa fa-edit fa-fw"></i> Aweber</div>
    <div class="panel-body">
        <div class="alert" id="aweber_message" style="display:none;"></div>
        <div class="form-group">
            <label for="contest_beast_aweber_auth_code">Auth Code</label>
            <div class="help-block"><p>To connect your Aweber account:</p>
                <ul>
                    <li><span>1.</span> <a href="https://auth.aweber.com/1.0/oauth/authorize_app/535ce49e" class="button" target="_blank">Click here</a>
                        <span>to open the authorization page and log in.</span></li>
                    <li><span>2.</span> Copy and paste the authorization code in the field below.</li>
                </ul>
            </div>
            <textarea class="form-control" id="contest_beast_aweber_auth_code" row="4" col="40"><?php echo get_user_meta($current_user->ID, '_aweber_auth_code', true); ?></textarea>
        </div>
        <div class="form-group">
            <label for="contest_beast_aweber_list_name">List Name</label>
            <input class="form-control" type="text" id="contest_beast_aweber_list_name" value="<?php echo get_user_meta($current_user->ID, '_aweber_list_name', true); ?>">
            <div class="help-block">(It can be found in your campaign or lists settings)</div>
            <button id="aweberapiverify" class="btn btn-info">
                Save
            </button>
        </div>
    </div>
</div>


<div class="panel panel-default">
    <div class="panel-heading"><i class="fa fa-edit fa-fw"></i> GetResponse</div>
    <div class="panel-body">
        <div class="form-group">
            <label class="control-label" for="getresponseApiKey" style="display:block;">Enter your API KEY</label>
            <input id="getresponseApiKey" value="<?= get_user_meta($current_user->ID, '_getreponseApi_api', true) ?>"
                   name="" type="text" style="width:400px;position:relative; margin-right:6px; display:inline-block"
                   class="form-control input-md">
            <button style="position:relative; display:inline-block" id="getresponseverify" class="btn btn-default">
                Verify
            </button>
        </div>
        <div class="getresponselistIds" id="getresponselistIds">
            <?php
            /*  delete_user_meta($current_user->ID, '_getreponseApi_api');
             delete_user_meta($current_user->ID, '_getreponseApi_list');
              */
            if (get_user_meta($current_user->ID, '_getreponseApi_api', true) != "") {
                $lists = get_user_meta($current_user->ID, '_getreponseApi_list', true);
                $da = 'geneterryrejano';
                /* echo "<pre>";
                print_r($lists);

                if (preg_match('/"'.$da.'"/i' , json_encode($lists))){
                    echo "T2giI";
                }

                if (in_array($da,$lists,true)){
                    echo "T2giI";
                }
                echo "</pre>"; */
                ?>
                You have <?= count($lists) ?> lists to your MailChimp account.
                <div style="padding:10px 0px;" id="mailchimprest">
                    <table class="table table-bordered" style="width:320px !important">
                        <thead>
                        <tr>
                            <th>List Id</th>
                            <th>List Name</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (count($lists) > 1) {
                            $h = 0;
                            foreach ($lists as $lst_key => $lst_val) {

                                echo '<tr>';
                                echo '<td><input type="hidden" name="gr_list[' . $h . ']" value="' . $lst_val['list_id'] . '">' . $lst_val['list_id'] . '</td>';
                                echo '<td><input type="hidden" name="gr_name[' . $h . ']" value="' . $lst_val['list_name'] . '">' . $lst_val['list_name'] . '</td>';
                                echo '</tr>';
                                $h++;
                            }

                        } else {
                            echo '<tr>';
                            echo '<td><input type="hidden" name="gr_list[0]" value="' . $lists[0]['list_id'] . '">' . $lists[0]['list_id'] . '</td>';
                            echo '<td><input type="hidden" name="gr_name[0]" value="' . $lists[0]['list_name'] . '">' . $lists[0]['list_name'] . '</td>';
                            echo '</tr>';
                        }
                        ?>
                        </tbody>
                    </table>
                    <div style="padding:15px 0px 5px">

                        <button class="btn btn-info" id="getreponse_save">Save</button>
                        <button class="btn btn-defualt">Cancel</button>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading"><i class="fa fa-edit fa-fw"></i> iContact</div>
    <div class="panel-body">
        <div class="form-group">
            <label class="control-label" for="getresponseApiKey" style="display:block;">Enter your API KEY</label>
            <input id="getresponseApiKey" value="<?= get_user_meta($current_user->ID, '_getreponseApi_api', true) ?>"
                   name="" type="text" style="width:400px;position:relative; margin-right:6px; display:inline-block"
                   class="form-control input-md">
            <button style="position:relative; display:inline-block" id="getresponseverify" class="btn btn-default">
                Verify
            </button>
        </div>
        <div class="getresponselistIds" id="getresponselistIds">
            <?php
            /*  delete_user_meta($current_user->ID, '_getreponseApi_api');
             delete_user_meta($current_user->ID, '_getreponseApi_list');
              */
            if (get_user_meta($current_user->ID, '_getreponseApi_api', true) != "") {
                $lists = get_user_meta($current_user->ID, '_getreponseApi_list', true);
                $da = 'geneterryrejano';
                /* echo "<pre>";
                print_r($lists);

                if (preg_match('/"'.$da.'"/i' , json_encode($lists))){
                    echo "T2giI";
                }

                if (in_array($da,$lists,true)){
                    echo "T2giI";
                }
                echo "</pre>"; */
                ?>
                You have <?= count($lists) ?> lists to your MailChimp account.
                <div style="padding:10px 0px;" id="mailchimprest">
                    <table class="table table-bordered" style="width:320px !important">
                        <thead>
                        <tr>
                            <th>List Id</th>
                            <th>List Name</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (count($lists) > 1) {
                            $h = 0;
                            foreach ($lists as $lst_key => $lst_val) {

                                echo '<tr>';
                                echo '<td><input type="hidden" name="gr_list[' . $h . ']" value="' . $lst_val['list_id'] . '">' . $lst_val['list_id'] . '</td>';
                                echo '<td><input type="hidden" name="gr_name[' . $h . ']" value="' . $lst_val['list_name'] . '">' . $lst_val['list_name'] . '</td>';
                                echo '</tr>';
                                $h++;
                            }

                        } else {
                            echo '<tr>';
                            echo '<td><input type="hidden" name="gr_list[0]" value="' . $lists[0]['list_id'] . '">' . $lists[0]['list_id'] . '</td>';
                            echo '<td><input type="hidden" name="gr_name[0]" value="' . $lists[0]['list_name'] . '">' . $lists[0]['list_name'] . '</td>';
                            echo '</tr>';
                        }
                        ?>
                        </tbody>
                    </table>
                    <div style="padding:15px 0px 5px">

                        <button class="btn btn-info" id="getreponse_save">Save</button>
                        <button class="btn btn-defualt">Cancel</button>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>