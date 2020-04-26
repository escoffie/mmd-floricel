<?php
namespace LocoAutoTranslateAddon\Register;
  
require_once "LocoAutomaticTranslateAddonProBase.php";
	class LocoAutomaticTranslateAddonPro {
        public $plugin_file=__FILE__;
        public $responseObj;
        public $licenseMessage;
        public $showMessage=false;
        public $slug="loco-atlt-register";
        function __construct() {
    	    add_action( 'admin_print_styles', [ $this, 'SetAdminStyle' ] );
    	    $licenseKey=get_option("LocoAutomaticTranslateAddonPro_lic_Key","");
    	    $liceEmail=get_option( "LocoAutomaticTranslateAddonPro_lic_email","");
            LocoAutomaticTranslateAddonProBase::addOnDelete(function(){
               delete_option("LocoAutomaticTranslateAddonPro_lic_Key");
            });
    	    if(LocoAutomaticTranslateAddonProBase::CheckWPPlugin($licenseKey,$liceEmail,$this->licenseMessage,$this->responseObj,__FILE__)){
    		    add_action( 'admin_menu', [$this,'ActiveAdminMenu'],101);
    		    add_action( 'admin_post_LocoAutomaticTranslateAddonPro_el_deactivate_license', [ $this, 'action_deactivate_license' ] );
    		    //$this->licenselMessage=$this->mess;
                update_option("atlt-type","pro");
                

    	    }else{
    	        if(!empty($licenseKey) && !empty($this->licenseMessage)){
    	           $this->showMessage=true;
                }
                update_option("atlt-type","free");
    		    update_option("LocoAutomaticTranslateAddonPro_lic_Key","") || add_option("LocoAutomaticTranslateAddonPro_lic_Key","");
    		    add_action( 'admin_post_LocoAutomaticTranslateAddonPro_el_activate_license', [ $this, 'action_activate_license' ] );
    		    add_action( 'admin_menu', [$this,'InactiveMenu'],101);
    	    }
        }
    	function SetAdminStyle() {
    		wp_register_style( "LocoAutomaticTranslateAddonProLic", plugins_url("style.css",$this->plugin_file),10);
    		wp_enqueue_style( "LocoAutomaticTranslateAddonProLic" );
    	}
        function ActiveAdminMenu(){
                add_submenu_page( 'loco',
                'Loco Automatic Translate Addon Pro', 
                'Auto Translator Addon - Premium License',
                 'manage_options', 
                    $this->slug,
                 array($this, 'Activated'));
        }
        function InactiveMenu() {
            add_submenu_page( 'loco',
            'Loco Automatic Translate Addon Pro', 
            'Auto Translator Addon - Premium License',
             'activate_plugins', 
             $this->slug,
             array($this, 'LicenseForm'));
    	  /*  add_menu_page( "LocoAutomaticTranslateAddonPro", "Loco Automatic Translate Addon Pro", 'activate_plugins', $this->slug,  [$this,"LicenseForm"], " dashicons-star-filled " ); */

        }
        function action_activate_license(){
        		check_admin_referer( 'el-license' );
        		$licenseKey=!empty($_POST['el_license_key'])?$_POST['el_license_key']:"";
        		$licenseEmail=!empty($_POST['el_license_email'])?$_POST['el_license_email']:"";
        		update_option("LocoAutomaticTranslateAddonPro_lic_Key",$licenseKey) || add_option("LocoAutomaticTranslateAddonPro_lic_Key",$licenseKey);
        		update_option("LocoAutomaticTranslateAddonPro_lic_email",$licenseEmail) || add_option("LocoAutomaticTranslateAddonPro_lic_email",$licenseEmail);
        		wp_safe_redirect(admin_url( 'admin.php?page='.$this->slug));
        	}
        function action_deactivate_license() {
    	    check_admin_referer( 'el-license' );
    	    if(LocoAutomaticTranslateAddonProBase::RemoveLicenseKey(__FILE__,$message)){
    		    update_option("LocoAutomaticTranslateAddonPro_lic_Key","") || add_option("LocoAutomaticTranslateAddonPro_lic_Key","");
    	    }
    	    wp_safe_redirect(admin_url( 'admin.php?page='.$this->slug));
        }
        function Activated(){
            ?>
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <input type="hidden" name="action" value="LocoAutomaticTranslateAddonPro_el_deactivate_license"/>
                <div class="el-license-container">
                    <h3 class="el-license-title"><i class="dashicons-before dashicons-star-filled"></i> <?php _e("Loco Automatic Translate Addon - Premium License Status",$this->slug);?> </h3>
                    <hr>
                    <ul class="el-license-info">
                    <li>
                        <div>
                            <span class="el-license-info-title"><?php _e("License Status",$this->slug);?></span>

    			            <?php if ( $this->responseObj->is_valid ) : ?>
                                <span class="el-license-valid"><?php _e("Valid",$this->slug);?></span>
    			            <?php else : ?>
                                <span class="el-license-valid"><?php _e("Invalid",$this->slug);?></span>
    			            <?php endif; ?>
                        </div>
                    </li>

                    <li>
                        <div>
                            <span class="el-license-info-title"><?php _e("License Type",$this->slug);?></span>
    			            <?php echo $this->responseObj->license_title; ?>
                        </div>
                    </li>

                    <li>
                        <div>
                            <span class="el-license-info-title"><?php _e("License Expiry Date",$this->slug);?></span>
    			            <?php echo $this->responseObj->expire_date; ?>
                        </div>
                    </li>

                    <li>
                        <div>
                            <span class="el-license-info-title"><?php _e("Support Expiry Date",$this->slug);?></span>
    			            <?php echo $this->responseObj->support_end; ?>
                        </div>
                    </li>
                        <li>
                            <div>
                                <span class="el-license-info-title"><?php _e("Your License Key",$this->slug);?></span>
                                <span class="el-license-key"><?php echo esc_attr( substr($this->responseObj->license_key,0,9)."XXXXXXXX-XXXXXXXX".substr($this->responseObj->license_key,-9) ); ?></span>
                            </div>
                        </li>
                    </ul>
                    <div class="el-license-active-btn">
    				    <?php wp_nonce_field( 'el-license' ); ?>
    				    <?php submit_button('Deactivate License'); ?>
                    </div>
                </div>
            </form>
    	<?php
        }

        function LicenseForm() {
    	    ?>
        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
    	    <input type="hidden" name="action" value="LocoAutomaticTranslateAddonPro_el_activate_license"/>
    	    <div class="el-license-container">
    		    <h3 class="el-license-title"><i class="dashicons-before dashicons-star-filled"></i> <?php _e("Loco Automatic Translate Addon - Premium License",$this->slug);?></h3>
    		    <hr>
                <?php
                if(!empty($this->showMessage) && !empty($this->licenseMessage)){
                    ?>
                    <div class="notice notice-error is-dismissible">
                        <p><?php echo _e($this->licenseMessage,$this->slug); ?></p>
                    </div>
                    <?php
                }
                ?>
    		    <p><?php _e("Enter your license key below to <strong>increase auto translation limit</strong> and get premium support & features.",$this->slug);?></p>
                <style>
                table.loco-addon-license tr th, table.loco-addon-license tr td {
                    border: 1px solid #bbb;
                    padding: 12px;
                    text-align: center;
                }
                </style>
                <table class="loco-addon-license">
                <tr>
                <th>Features</th>
                <th>Free License</th>
                <th>Premium License</th>
                </tr>
                <tr>
                <td>Yandex Translate API</td>
                <td>Available</td>
                <td>Available</td>
                </tr>
                <tr>
                <td>Yandex API - free translation limit</td>
                <td>300,000 char / day<br/>1,000,000 char / month</td>
                <td>1,000,000 char / day<br/>10,000,000 char / month</td>
                </tr>
                <tr>
                <td>Google Translate API</td>
                <td>Not Available</td>
                <td>Available</td>
                </tr>
                <tr>
                <td>Google API - free translation limit</td>
                <td>Not Available</td>
                <td>500,000 char / month</td>
                </tr>
                <td>Microsoft Translator API</td>
                <td>Not Available</td>
                <td>Available</td>
                </tr>
                <tr>
                <td>Microsoft API - free translation limit</td>
                <td>Not Available</td>
                <td>2,000,000 char / month</td>
                </tr>
                <tr>
                <td>HTML Translation Support</td>
                <td>Not Available</td>
                <td>Available for Yandex API</td>
                </tr>
                <tr>
                <td>Reset Translations</td>
                <td>Not Available</td>
                <td>Available</td>
                </tr>
                <td>Support</td>
               <td>WordPress Free Forum Support!<br/><strong>(Support Time: 7 – 10 days)</strong></td>
               <td>Quick Support Via Email<br/><strong>contact@coolplugins.net</strong></td>
                </tr>
                </table>
                <p>Free characters translation limit only provided by Translate API providers, e.g. - Google, Microsoft, Yandex etc.<br/>Plugin don't provide any free characters limit for automatic translations.</p>
                <br/>
                <h3><a href='https://locotranslate.com/addon/loco-automatic-translate-premium-license-key/#pricing' target='_blank'>Buy Premium License Key - <strong>($12 - $89)</strong></a></h3>
                <br/>
    		    <div class="el-license-field">
    			    <label for="el_license_key"><?php _e("Enter License code",$this->slug);?></label>
    			    <input type="text" class="regular-text code" name="el_license_key" size="50" placeholder="xxxxxxxx-xxxxxxxx-xxxxxxxx-xxxxxxxx" required="required">
    		    </div>
                <div class="el-license-field">
                    <label for="el_license_key"><?php _e("Email Address",$this->slug);?></label>
                    <?php
                        $purchaseEmail   = get_option( "LocoAutomaticTranslateAddonPro_lic_email", get_bloginfo( 'admin_email' ));
                    ?>
                    <input type="text" class="regular-text code" name="el_license_email" size="50" value="<?php echo sanitize_email($purchaseEmail); ?>" placeholder="" required="required">
                    <div><small><?php _e("✅ I agree to share my purchase code and email for plugin verification and to receive future updates notifications!",$this->slug);?></small></div>
                </div>
    		    <div class="el-license-active-btn">
    			    <?php wp_nonce_field( 'el-license' ); ?>
    			    <?php submit_button('Activate'); ?>
    		    </div>
    	    </div>
        </form>
    	    <?php
        }
    }

    new LocoAutomaticTranslateAddonPro();