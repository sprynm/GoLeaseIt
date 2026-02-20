<?php
// Only open a new form if one doesn't already exist.
if (!isset($createForm)):
    $createForm = true;
endif;

if (!isset($showOptions)):
	$showOptions = true;
endif;

if ($showOptions):
?>
    <div class="paginate-form">
    <?php
    if ($createForm):
        echo $this->Form->create($this->Form->model(), array('url' => '/'.$this->request->url));
    endif;

    echo $this->Form->input('paginate', array(
        'name' => 'paginate',
        'label' => __('Results per page:'),
        'options' => array_combine($paginationOptions, $paginationOptions),
        'default' => $paginationLimit,
        'id' => 'results-per-page',
        'div' => false
    ));
    echo $this->Form->submit('Go', array('div' => false, 'name' => 'results-per-page-submit'));

    if ($createForm):
        echo $this->Form->end();
    endif;
    ?>
    </div>
<?php endif; // if ($showOptions): ?>
<p class="pagination-top-text">
<?php
echo $this->Paginator->counter(array(
'format' => __('Page %page% of %pages%, showing records %start%-%end% of %count%.')
));
?>
</p>
