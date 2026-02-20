<?php
$this->Html->scriptBlock("
$(document).ready(function() {
        $('a.add_new').click(function() {
                var idString = $(this).attr('id');
                var info = idString.split('-');
                var divs = $('.new_field_'+info[0]);
                var last = divs[divs.length-1];
                var count = $(last).attr('rel');
                if (!count) {
                    count = 0;
                }
                var url = '/admin/prototype/prototype_instances/new_field/type:'+info[0]+'/id:'+info[1]+'/count:'+count;
                var h4 = $(this).parent();
                $.get(
                        url,
                        null,
                        function(responseText) {
                            $(responseText).appendTo(h4.prev('table.prototype-field-table').children('tbody'));
                        },
                        'html'
                );
                return false;
        });
});
", array('inline' => false));
?>
