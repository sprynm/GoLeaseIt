<style>
	.disabled, .disabled:hover, input[type="submit"]:disabled{
		background-color: gray;

	}
</style>
<?php
$pluginsArray = "[";
foreach($plugins as $plugin) {
	$pluginsArray .= '"' . $plugin . '",';
}
$pluginsArray = rtrim($pluginsArray, ',') . "]";


$this->Html->scriptBlock('
	
', array('inline' => false)); 

?>

<script>
$(function() {
		var plugins = <?php echo $pluginsArray; ?>;
		$("#PrototypeInstanceName").on("blur change keyup",function(e){
			if($.inArray($("#PrototypeInstanceName").val().trim(), plugins) !== -1){
				if($("#dupe_warning").length === 0) {
					$("#PrototypeInstanceName").before("<div id=\'dupe_warning\' class=\'notification notification--error png_bg\'>There is a plugin with this name or alias. Please choose something else.</div>");
				}
				$("input[type=\'submit\']").prop("disabled", true);
			} else {
				$("#dupe_warning").remove();
				$("input[type=\'submit\']").prop("disabled", false);
			}

		});
	});
</script>
