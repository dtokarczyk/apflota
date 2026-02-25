<?php

add_action('admin_menu', 'ac_cookie_menu');
function ac_cookie_menu(): void
{
    add_menu_page(
        __('Cookie Settings Page', 'textdomain'),
        'Cookie Settings',
        'manage_options',
        'ac_custom_cookie_page',
        'ac_settings_cookie_page',
        'dashicons-star-half',
        300,
    );
    add_action('admin_init', 'register_cookie_settings');
}
function register_cookie_settings(): void
{
    register_setting('ac-settings-group', 'ac_cookie_id');
}
function ac_settings_cookie_page(): void
{
    ?>
<div class="wrap">
<form method="post" action="options.php">
    <?php settings_fields('ac-settings-group'); ?>
    <?php do_settings_sections('ac-settings-group'); ?>
    <table class="form-table">
    	<tr valign="top">
        	<th scope="row"></th>
        	<td><h3>Cookie Settings</h3></td>
        </tr>
            <tr valign="top">
            <th scope="row">Select cookie page</th>
            <td>
                <?php
                        $args = [
                            'depth'                 => -1,
                            'selected'              => get_option('ac_cookie_id'),
                            'echo'                  => 1,
                            'name'                  => 'ac_cookie_id',
                        ];
    wp_dropdown_pages($args);
    ?>
            </td>
        </tr>
     </table>
    <?php submit_button(); ?>
</form>

</div>
<?php }
function return_cookie_url()
{
    $id = get_option('ac_cookie_id');
    $url = get_the_permalink($id);
    if (function_exists('icl_object_id')) {
        $url = get_the_permalink(icl_object_id($id, 'page', false, ICL_LANGUAGE_CODE));
    }
    return $url;
}
function ac_show_cookie_bar(): void
{
    ob_start();
    ?>
    <div id='ac_cookie_bar'>
        <div>
            <div>
                <?php
                    $id = get_option('ac_cookie_id');
    $url = get_the_permalink($id);
    if (function_exists('icl_object_id')) {
        $url = get_the_permalink(icl_object_id($id, 'page', false, ICL_LANGUAGE_CODE));
    }
    ?>
                <div><?php echo get_field('cookies_-_info', wpmlID(2)); ?></div>
                <div id="ac_close_cookie_bar" onclick="wi_create_cookie('wi_cookie_info','hide',100);wi_remove_cookie_bar();"><?php echo get_field('cookies_-_close', wpmlID(2)); ?></div>
            </div>
        </div>
    </div>
    <script>
        function wi_remove_cookie_bar(){
            document.getElementById("ac_cookie_bar").remove();
        }
        function wi_create_cookie(name, value, days) {
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                var expires = "; expires=" + date.toGMTString();
            }
            else var expires = "";
            document.cookie = name + "=" + value + expires + "; path=/";
        }
        function wi_read_cookie(name) {
            var nameEQ = name + "=";
            var ca = document.cookie.split(';');
            for(var i=0;i < ca.length;i++) {
                var c = ca[i];
                while (c.charAt(0)==' ') c = c.substring(1,c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
            }
            return null;
        }
        if(wi_read_cookie('wi_cookie_info') == 'hide') {
            document.getElementById("ac_cookie_bar").remove();
        }
    </script>
    <?php
    ob_end_flush();
}

function ac_cookie_add_footer_styles(): void
{
    ob_start();
    ?>
        <style>
            #ac_cookie_bar{
                position: fixed;
                width: 100%;
                box-sizing: border-box;
                background-color: rgba(0, 0, 0, 0.9);
                color: white;
                padding: 10px 0;
                z-index: 999999;
                bottom:0;
                left:0;
		font-weight: normal;
                font-size: 14px;
		line-height: 1.2;
                right: 0;
            }
            #ac_cookie_bar p{
                margin-bottom: 0!important;
            }
            #ac_cookie_bar > div > div {
                width: 100%;
                padding: 0 calc(100% / 16 * 2);
            }
            @media (max-width: 1600px) {
                #ac_cookie_bar > div > div {
                    padding: 0 calc(100% / 16 * 1.5);
                }
            }
            @media (max-width: 1350px) {
                #ac_cookie_bar > div > div {
                    padding: 0 calc(100% / 16 * 1);
                }
            }
            @media (max-width: 992px) {
                #ac_cookie_bar > div > div {
                    padding: 0 20px;
                }
            }
            #ac_cookie_bar > div > div{
                display: inline-flex;
                -webkit-align-items: center; /* Safari 7.0+ */
                align-items: center;
                width: 100%;
            }
            #ac_cookie_bar > div > div > div:nth-of-type(1){
                box-sizing: border-box;
                width: calc(100% - 80px);
            }
            #ac_cookie_bar > div > div > div:nth-of-type(2){
                box-sizing: border-box;
                padding: 5px 0;
                cursor: pointer;
                width: 80px;
                text-align: right;
            }
            #ac_cookie_bar a{
                color:red;
                font-weight: 500;
            }
            .cookies td {
                border: 1px solid #ddd;
                font-size: 1.2rem;
                padding: 5px 10px;
            }
        </style>
    <?php
    ob_end_flush();
}

if (!isset($_COOKIE['wi_cookie_info'])) {
    add_action('get_footer', 'ac_cookie_add_footer_styles');
    add_action('get_footer', 'ac_show_cookie_bar');
}
