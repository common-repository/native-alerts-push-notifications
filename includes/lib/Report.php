<?php
/**
 * Class for rendering reports
 * 
 * @author Adiant
 *
 */
class NativeAlertsPush_Report  {
	protected $title;			// title of the report
	protected $name;				// embedded name in the elements
	protected $cols; 			// array of json elements containing the column information
	protected $datasource;	// initial datasource
	protected $token;			// token for retrieving data
	protected $config;

	public function __construct($title, $name, $cols, $config, $datasource, $token, $apiUrl) {
		$this->title 			= $title;
		$this->name 			= $name;
		$this->cols 			= $cols;
		$this->config 		= $config;
		$this->datasource = $datasource;
		$this->token 			= $token;
		$this->apiUrl			= $apiUrl;
		return $this;
	}

	public function render() {
		$html = <<< HTML

			<h3>{$this->title}</h3>


			Start Date: <input type="text" id="nativealerts_{$this->name}_startdate" class="nativealerts_date_field">
			End Date: <input type="text" id="nativealerts_{$this->name}_enddate" class="nativealerts_date_field">
			<input type="button" id="nativealerts_{$this->name}_refresh" value="Refresh"/>
			<div id="nativealerts_{$this->name}_container"></div>
			<script type="text/javascript">
			var $ = jQuery;
			$(document).ready(function() {
			    var dataSource = {$this->datasource},
			    	cols = [{$this->cols}],
			     	config = {$this->config},
			    	paginated = new ABPaginator(config);
			    	
			    paginated.init();

			    $('#nativealerts_{$this->name}_startdate').datepicker({ dateFormat: "yy-mm-dd" });
			    $('#nativealerts_{$this->name}_enddate').datepicker({ dateFormat: "yy-mm-dd" });
			    $('#nativealerts_{$this->name}_startdate').datepicker('setDate', new Date());
			    $('#nativealerts_{$this->name}_enddate').datepicker('setDate', new Date());

			    $('#nativealerts_{$this->name}_refresh').click (function() {
			      var url = '{$this->apiUrl}' +'/' + $('#nativealerts_{$this->name}_startdate').datepicker().val() + 
			        '/' + $('#nativealerts_{$this->name}_enddate').datepicker().val();

			      var opt = {
			        url : url,
			        type : 'GET',
			        dataType : 'json',
			        contentType: 'application/json; charset=utf-8',
			        headers : {
			          token: '{$this->token}'
			        },
			        success : function (result) {
			          paginated.refresh(result);
			        },
			        error : function (error) {
			          if (error.responseJSON) {
			            alert(error.responseJSON.message);
			          } else {
			            alert('unexpected error. see console.');
			            console.log(error);
			          }
			        }
			      };
			      $.ajax(opt);
			    });
			});

			</script>


HTML;
			return $html;
	}


}

