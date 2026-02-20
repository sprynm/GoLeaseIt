<?php $this->extend('Administration.Common/index-page'); ?>
<?php
$this->set('header', 'Prototype Instances');
?>
<?php
$this->start('actionLinks');
echo $this->AdminLink->link(__('New Instance'), array('action'=>'edit'));
$this->end('actionLinks');
?>

<?php
echo $this->Form->create('PrototypeInstance', array('url' => $this->request->here, 'class' => 'preconfigured-form'));
echo $this->Form->input('preconfigured', array(
    'type' => 'select',
    'options' => $preconfigured,
    'empty' => 'Install a Preconfigured Instance',
    'label' => false,
    'div' => false
));
echo $this->Form->end('Go');
?>

<table class="admin-table">
    <?php echo $this->element('Administration.index/table_caption'); ?>
    <thead>
        <tr>
            <th><?php echo $this->Paginator->sort('name');?></th>
            <th><?php echo $this->Paginator->sort('PublishingInformation.start', 'Start Publishing');?></th>
            <th><?php echo $this->Paginator->sort('PublishingInformation.end', 'End Publishing');?></th>
            <th class="icon-column">Published</th>
            <?php echo $this->element('Administration.index/actions_header'); ?>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($prototypeInstances as $prototypeInstance): ?>
        <tr>
            <td><?php echo $prototypeInstance['PrototypeInstance']['name']; ?></td>
            <td><?php echo $this->Publishing->start($prototypeInstance['PublishingInformation']['start']); ?></td>
            <td><?php echo $this->Publishing->end($prototypeInstance['PublishingInformation']['end']); ?></td>
            <td>
            <?php
            echo $this->Publishing->toggle(array('data' => $prototypeInstance, 'model' => 'Prototype.PrototypeInstance'));
            ?>
            </td>
            <?php echo $this->element('Administration.index/actions_column', array('item' => $prototypeInstance['PrototypeInstance'])); ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php echo $this->element('Administration.index/table_footer'); ?>
