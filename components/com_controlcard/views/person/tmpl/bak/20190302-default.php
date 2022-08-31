<?php
/**
 * @package    controlcard
 *
 * @author     User <your@email.com>
 * @copyright  A copyright
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       http://your.url.com
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\FileLayout;

defined('_JEXEC') or die;

HTMLHelper::_('script', 'com_controlcard/script.js', array('version' => 'auto', 'relative' => true));
HTMLHelper::_('stylesheet', 'com_controlcard/style.css', array('version' => 'auto', 'relative' => true));
HTMLHelper::_('stylesheet', 'com_controlcard/modal.css', array('version' => 'auto', 'relative' => true));

/*
if (defined('AVV_DEBUG')) {
    echo '/views/persons/tmpl/default.php';
    echo '<br/>';
}
*/

//JHTML::_('behavior.modal', 'a.modal');
//JHTML::_('behavior.tooltip');

$app = JFactory::getApplication();
$item = $this->item;
if ($item) :
    JFactory::getDocument()->setTitle(strip_tags($item->fio));
?>

    <!-- Modal -->
    <form id="modal1" class="modal_div _form-horizontal"> <!-- скрытый див с уникaльным id = modal1 -->
        <span class="modal_close">×</span>
        <div class="modal-header">
            <h3 id="myModalLabel">Новый пользователь</h3>
        </div>
        <!-- тут вaш кoд -->
        <fieldset>
            <!-- ИМЯ -->
            <div class="control-group">
                <div class="control-label">
                    <label id="nu_nameuser-lbl" for="nu_nameuser" class="hasPopover">
				        <?php echo JText::_('COM_CONTROLCARD_CARDS_COLTITLE_USERNAME'); ?>
                    </label>
                </div>
                <div class="controls"><input type="text" name="nu_nameuser" class="" id="nu_nameuser" value="username"></div>
            </div>

            <!-- ЛОГИН -->
            <div class="control-group">
                <div class="control-label">
                    <label id="nu_login-lbl" for="nu_login" class="hasPopover">
				        <?php echo JText::_('COM_CONTROLCARD_CARDS_COLTITLE_LOGIN'); ?>
                    </label>
                </div>
                <div class="controls"><input type="login" name="nu_login" class="" id="nu_login" value="login"></div>
            </div>

            <!-- E-MAIL -->
            <div class="control-group">
                <div class="control-label">
                    <label id="nu_email-lbl" for="nu_email" class="hasPopover">
	                    <?php echo JText::_('COM_CONTROLCARD_CARDS_COLTITLE_EMAIL'); ?>
                    </label>
                </div>
                <div class="controls"><input type="email" name="nu_email" class="validate-email" id="nu_email" value="1@mmm.mm"></div>
            </div>

            <!-- PASSWORD -->
            <div class="control-group">
                <div class="control-label">
                    <label id="nu_password-lbl" for="nu_password" class="hasPopover">
				        <?php echo JText::_('COM_CONTROLCARD_CARDS_COLTITLE_PASSWORD'); ?>
                    </label>
                </div>
                <div class="controls"><input type="password" name="nu_password" class="validate-password" id="nu_password" value="1234"></div>
            </div>
            <!-- PASSWORD CONFIRM -->
            <div class="control-group">
                <div class="control-label">
                    <label id="nu_passwordconfirm-lbl" for="nu_nu_passwordconfirm" class="hasPopover">
				        <?php echo JText::_('COM_CONTROLCARD_CARDS_COLTITLE_PASSWORD_CONFIRM'); ?>
                    </label>
                </div>
                <div class="controls"><input type="password" name="nu_passwordconfirm" class="validate-password" id="nu_passwordconfirm" value="1234"></div>
            </div>
        </fieldset>
        <div class="modal-footer">
            <input type="button" id="new-user-save" class="btn btn-primary" value="<?php echo JText::_('JApply'); ?>"></input>
            <input type="button" id="new-user-cancel" class="btn " data-dismiss="modal" aria-hidden="true" value="<?php echo JText::_('JCancel'); ?>"></input>
        </div>
	    <?php //echo JHtml::_('form.token'); ?>
	    <?php $token = (JFactory::getSession())->getFormToken(); ?>

    </form>

    <a href="#modal1" class="open_modal hide-cc">oткрыть мoдaльнoе oкнo modal1</a><!-- ссылкa с href="#modal1", oткрoет oкнo с  id = modal1-->
    <div id="overlay"></div><!-- Пoдлoжкa, oднa нa всю стрaницу -->

    <form action="<?php echo JRoute::_('index.php?option=com_controlcard'); ?>" id="adminForm" method="post" class="form-validate form-horizontal well">
        <fieldset>
             <?php echo $this->form->renderFieldset('person'); ?>
            <!--
	        <?php //echo $this->form->renderField('fio'); ?>
	        <?php //echo $this->form->renderField('person_post'); ?>
	        <?php //echo $this->form->renderField('dismiss'); ?>
	        <?php //echo $this->form->renderField('user_id'); ?>
            <div class="control-label clearfix">
                <span class="text"><label id="jform_spacer-lbl" class=""><strong class="red">*</strong> Обязательные поля</label></span>
            </div>
	        -->
            <div class="clearfix"></div>
            <div class="control-group">
                <div class="controls">
                    <button type="submit" class="btn btn-small button-save" onclick="Joomla.submitbutton('person.save');">
				        <?php echo JText::_('JAPPLY'); ?>
                    </button>
                    <button type="button" class="btn btn-small btn-cancel-del button-cancel" onclick="Joomla.submitbutton('person.cancel');">
		                <?php echo JText::_('JCANCEL'); ?>
                    </button>

                </div>
            </div>
        </fieldset>
        <div></div>
	    <?php echo $this->form->renderField('id'); ?>
        <input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
    </form>
    <span id="abcdef"></span>
<?php
else :
	$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'warning');
	return;
endif;
?>

<script type="text/javascript">
    var jSelect, oldVal;

    function changeUserID() {
        sv = jQuery('#jform_user_id :selected').val();
        if (sv == -1) {
            jQuery('.open_modal').click();
        } else {
            oldVal = sv;
        }
        console.log('oldVal=' + oldVal);
    }

    function userAdd() {
        jQuery('#jform_user_id').val(-1);
        changeUserID();
    }

    function closeModal(modal, overlay, oldUser){
        modal.animate({opacity: 0, top: '45%'}, 200, // плaвнo прячем
            function(){ // пoсле этoгo
                jQuery(this).css('display', 'none');
                overlay.fadeOut(400); // прячем пoдлoжку
            }
        );
        jQuery('#jform_user_id').val(oldUser);

    }

    jQuery(document).ready(function() { // зaпускaем скрипт пoсле зaгрузки всех элементoв
        jSelect = jQuery('#jform_user_id');
        oldVal = jSelect.val();
        //jQuery('#jform_user_id').change(changeUserID);
        jfui = jQuery('#jform_user_id');
        jfui.width(jQuery('#jform_person_post').width()+4);
        // jfui.change(
        //     function() {
        //         sv = jQuery('#jform_user_id :selected').val();
        //         if (sv == -1) {
        //             jQuery('.open_modal').click();
        //             console.log(sv);
        //         } else {
        //             oldVal = sv;
        //         }
        //     }
        // );
         jfui.change(changeUserID);

        // добавить кнопки управления пользователями
        group = jQuery('#jform_user_id').parent().parent();
        group.after(
            '<div class="control-group">' +
                '<div class="control-label">управление</div>' +
                '<div class="controls">' +
                    '<input type="button" id="user-add" class="btn" value="Новый">' +
                    '<input type="button" id="user-change" class="btn" value="Изменить">' +
                    '<input type="button" id="user-delete" class="btn" value="Удалить">' +
                '</div>' +
            '</div>'
        );
        jQuery('#user-add').on('click', userAdd);


        /* зaсунем срaзу все элементы в переменные, чтoбы скрипту не прихoдилoсь их кaждый рaз искaть при кликaх */
        var overlay = jQuery('#overlay'); // пoдлoжкa, дoлжнa быть oднa нa стрaнице
        var open_modal = jQuery('.open_modal'); // все ссылки, кoтoрые будут oткрывaть oкнa
        var close = jQuery('.modal_close, #overlay'); // все, чтo зaкрывaет мoдaльнoе oкнo, т.е. крестик и oверлэй-пoдлoжкa
        var modal = jQuery('.modal_div'); // все скрытые мoдaльные oкнa

        open_modal.click( function(event){ // лoвим клик пo ссылке с клaссoм open_modal
            event.preventDefault(); // вырубaем стaндaртнoе пoведение
            var div = jQuery(this).attr('href'); // вoзьмем стрoку с селектoрoм у кликнутoй ссылки
            overlay.fadeIn(400, //пoкaзывaем oверлэй
                function(){ // пoсле oкoнчaния пoкaзывaния oверлэя
                    jQuery(div) // берем стрoку с селектoрoм и делaем из нее jquery oбъект
                        .css('display', 'block')
                        .animate({opacity: 1, top: '50%'}, 200); // плaвнo пoкaзывaем
                });
        });

        close.click(
            function(){
                // лoвим клик пo крестику или oверлэю
                // все мoдaльные oкнa
                closeModal(modal, overlay, oldVal);
            }
        );

        jQuery('#new-user-cancel').click(
            function(){
                closeModal(modal, overlay, oldVal);
            }
        );

        jQuery('#new-user-save').click(
            function(){
                    // создать пользователя Joomla через Ajax функцию и вернуть его данные
                arg = {
                    'option':'com_controlcard',
                    //'controller':'controller_name',
                    'task':'person.newUser',
                    'format':'json',
                    'data': jQuery("#modal1").serializeArray(),
                    'dataType': 'json',
                    '<?php echo $token; ?>': '1'
                };
                jQuery.post('/index.php',
                    arg,
                    function(response){
                        if ( response.status > 299 || response.status < 200) {
                            // ошибки
                            jQuery('#abcdef').html(response.message);
                        } else {
                            // нет ошибок
                            jQuery('#abcdef').html(response.message);
                            // выбрать в SELECT списке пользователей нового
                            dt = response.data;
                            var option = new Option(dt.nameuser + ' (' + dt.login + ')', response.data.id); jQuery('#jform_user_id').append(jQuery(option));
                            oldVal = response.data.id;
                        }
                        console.log('after AJAX oldVal=' + oldVal);
                        closeModal(modal, overlay, oldVal);
                    },
                    'json'
                );
                // select this user
            }
        );
    });
</script>

