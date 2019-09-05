<?php
	
class Stylisheve_OrderActionsButtons_Block_Adminhtml_Orderbutton_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "id";
				$this->_blockGroup = "orderactionsbuttons";
				$this->_controller = "adminhtml_orderbutton";
				$this->_updateButton("save", "label", Mage::helper("orderactionsbuttons")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("orderactionsbuttons")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("orderactionsbuttons")->__("Save And Continue Edit"),
					"onclick"   => "saveAndContinueEdit()",
					"class"     => "save",
				), -100);



				$this->_formScripts[] = "

							function saveAndContinueEdit(){
								editForm.submit($('edit_form').action+'back/edit/');
							}
							(function($){
                                $(document).ready(function() {
                                    //multi select input start with null,array of strings
                                    //select input start with '',string
//                                    if('1'==$('#check_opening_tickets').val()){
//                                        $('#check_opening_tickets').attr('checked', 'checked');
//                                    }
                                    
                                    var action_types = ".json_encode(Stylisheve_OrderActionsButtons_Block_Adminhtml_Orderbutton_Grid::getActionTypeValueArray()).";
                                    var action_types_values = Object.values(action_types);
                                    var current_action = $('#action_type').val();
                                    var spanElem = `<span class='required'>*</span>`;//$( `<span class='required'>*</span>`);
                                    var showContainer = function(_containerId, _elementId, _spanElem){
                                        if (!$('#'+_elementId).hasClass( 'required-entry')){
                                            //add class, add span, show container
                                            $('#'+_elementId).addClass('required-entry');
                                            if('order_tobe_status'===_elementId){
                                                $('#'+_elementId+' option:first').attr('selected', 'selected');
                                            }
                                            //spanElem.appendTo( 'tr#'+_containerId+' td.label label' );
                                            $('tr#'+_containerId+' td.label label').append($(_spanElem));
                                            $('#'+_containerId).show();
                                        }
                                    };
                                    var hideContainer = function(_containerId, _elementId){
                                        if ($('#'+_elementId).hasClass( 'required-entry')){
                                            //remove class, remove span, remove valid class if exist , reset values, hide container
                                            $('#'+_elementId).removeClass(($('#'+_elementId).is('.validation-failed')) ? 'validation-failed required-entry':'required-entry');
                                            $('#'+_containerId+' .label span').remove();
                                            $('#'+_elementId+' option').removeAttr('selected');
                                            $('#'+_containerId).hide();
                                        }
                                    };
                                    var updateForm = function(_action,_actions){
                                        switch(_action) {
                                          case _actions['Change Status For View Page']:
                                            
                                            //show order-tobe-status-container if not has class required-entry
                                            showContainer('order-tobe-status-container', 'order_tobe_status', spanElem);
                                            
                                            //hide order-removed-buttons-container if has class required-entry
                                            hideContainer('order-removed-buttons-container', 'order_removed_buttons');
                                            $('#order-check-tickets-container').hide();
                                            
                                            $('#order-check-warehouse-container').hide();
                                            $('#order-check-delivery-date-container').hide();
                                            
                                            //hide report-type-container if has class required-entry
                                            hideContainer('report-type-container', 'report_type');
                                            hideContainer('report-title-container', 'report_title');
                                            
                                            break;
                                          case _actions['Change Status For Grid Page']:
                                            
                                            //show order-tobe-status-container if not has class required-entry
                                            showContainer('order-tobe-status-container', 'order_tobe_status', spanElem);
                                            
                                            //hide order-removed-buttons-container if has class required-entry
                                            hideContainer('order-removed-buttons-container', 'order_removed_buttons');
                                            $('#order-check-tickets-container').hide();
                                            
                                            $('#order-check-warehouse-container').show();
                                            $('#order-check-delivery-date-container').show();
                                            
                                            //hide report-type-container if has class required-entry
                                            hideContainer('report-type-container', 'report_type');
                                            hideContainer('report-title-container', 'report_title');
                                            
                                            break;
                                          case _actions['Generate Report & Change Status']:
                                            
                                            //show order-tobe-status-container if not has class required-entry
                                            showContainer('order-tobe-status-container', 'order_tobe_status', spanElem);
                                            
                                            //hide order-removed-buttons-container if has class required-entry
                                            hideContainer('order-removed-buttons-container', 'order_removed_buttons');
                                            $('#order-check-tickets-container').hide();
                                            
                                            $('#order-check-warehouse-container').show();
                                            $('#order-check-delivery-date-container').show();
                                            
                                            //show report-type-container if not has class required-entry
                                            showContainer('report-type-container', 'report_type', spanElem);
                                            showContainer('report-title-container', 'report_title', spanElem);
                                            
                                            break;
                                          case _actions['Remove Buttons From View Page']:
                                            
                                            //show order-removed-buttons-container if not has class required-entry
                                            showContainer('order-removed-buttons-container', 'order_removed_buttons', spanElem);
                                            $('#order-check-tickets-container').show();
                                            
                                            //hide order-tobe-status-container if has class required-entry
                                            hideContainer('order-tobe-status-container', 'order_tobe_status');
                                            
                                            $('#order-check-warehouse-container').hide();
                                            $('#order-check-delivery-date-container').hide();
                                            
                                            //hide report-type-container if has class required-entry
                                            hideContainer('report-type-container', 'report_type');
                                            hideContainer('report-title-container', 'report_title');
                                            
                                            break;
                                          case _actions['Generate Report']:
                                          
                                            //hide order-tobe-status-container if has class required-entry
                                            hideContainer('order-tobe-status-container', 'order_tobe_status');
                                            
                                            //hide order-removed-buttons-container if has class required-entry
                                            hideContainer('order-removed-buttons-container', 'order_removed_buttons');
                                            $('#order-check-tickets-container').hide();
                                            
                                            $('#order-check-warehouse-container').show();
                                            $('#order-check-delivery-date-container').show();
                                            
                                            //show report-type-container if not has class required-entry
                                            showContainer('report-type-container', 'report_type', spanElem);
                                            showContainer('report-title-container', 'report_title', spanElem);
                                            
                                            break;
                                          default:
                                            
                                            //hide order-tobe-status-container if has class required-entry
                                            hideContainer('order-tobe-status-container', 'order_tobe_status');
                                            
                                            //hide order-removed-buttons-container if has class required-entry
                                            hideContainer('order-removed-buttons-container', 'order_removed_buttons');
                                            $('#order-check-tickets-container').hide();
                                            
                                            $('#order-check-warehouse-container').hide();
                                            $('#order-check-delivery-date-container').hide();
                                            
                                            //hide report-type-container if has class required-entry
                                            hideContainer('report-type-container', 'report_type');
                                            hideContainer('report-title-container', 'report_title');
                                            
                                            break;
                                        }//endSWITCH
                                    };//end updateForm
                                    
                                    //after load page get value of action_type
                                    updateForm(current_action, action_types);
                                    
                                    $('#orderactionsbuttons_form').on('change', '#action_type', function() {
                                      updateForm($('#action_type').val(), action_types);
                                    });
                                });//end.READY
							})(jQuery);
						";
		}

		public function getHeaderText()
		{
				if( Mage::registry("orderbutton_data") && Mage::registry("orderbutton_data")->getId() ){

				    return Mage::helper("orderactionsbuttons")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("orderbutton_data")->getId()));

				} 
				else{

				     return Mage::helper("orderactionsbuttons")->__("Add Item");

				}
		}
}