<?php
$abteilungen = [
	'marketing' => 'Marketing',
	'kundenkontakt' => 'Kundenkontakt',
	'entwicklung' => 'Entwicklung',
	'server' => 'Server/Infra.',
	'organisation' => 'Organisation/Personal',
	'product_m' => 'ProduktM.',
	'business_dev' => 'BusinessDev'
];

if (!isset($startedTime)) {
	?>
	<h3 class="startbar-headline">Start Time</h3>
	<?php echo $this->Form->create('Time',
		['url' => ['action' => 'start'], 'method' => 'post', 'class' => 'form-inline']); ?>
	<?php echo $this->Form->input('id'); ?>
	<?php echo $this->Form->input(
		'customer_id',
		['type' => 'hidden', 'value' => 37, 'class' => 'input-medium']
	); ?>
	<?php echo $this->Form->input(
		'task',
		[
			'type' => 'select',
			'label' => false,
			'empty' => 'Choose Team',
			'div' => false,
			'options' => $abteilungen,
			'class' => 'input-small'
		]
	); ?>
	<?php echo $this->Form->input(
		'note',
		['label' => false, 'div' => false, 'placeholder' => 'Tätigkeit', 'size' => '20', 'class' => 'input-medium']
	); ?>
	<?php echo $this->Form->input(
		'break',
		['label' => false, 'div' => false, 'placeholder' => 'Pause', 'size' => '3', 'class' => 'input-small']
	); ?>

	<?php echo $this->Form->end(
		[
			'label' => 'Start',
			'class' => 'btn btn-success',
			'div' => false,
		]
	); ?>
	<?php
} else {
	?>
	<h3 class="startbar-headline">Stop Time</h3>
	<?php echo $this->Form->create('Time',
		['url' => ['action' => 'stop'], 'method' => 'post', 'class' => 'form-inline']); ?>

	<?php echo $this->Form->input('id'); ?>
	<?php echo $this->Form->input(
		'customer_id',
		['type' => 'hidden', 'value' => 37, 'class' => 'input-medium']
	); ?>
	<?php echo $this->Form->input(
		'task',
		[
			'type' => 'select',
			'label' => false,
			'empty' => 'Choose Team',
			'div' => false,
			'options' => $abteilungen,
			'class' => 'input-small'
		]
	); ?>
	<?php echo $this->Form->input(
		'note',
		['label' => false, 'div' => false, 'placeholder' => 'Tätigkeit', 'size' => '20', 'class' => 'input-medium']
	); ?>
	<?php echo $this->Form->input(
		'break',
		['label' => false, 'div' => false, 'placeholder' => 'Pause', 'size' => '3', 'class' => 'input-small']
	); ?>

	<?php echo $this->Form->end(
		[
			'label' => 'Stop',
			'class' => 'btn btn-danger',
			'div' => false,
		]
	); ?>
	<?php
}
?>

<div class="row">
	<div class="span10">
		<table id="timetable" class=" table table-striped table-bordered table-condensed">
			<thead>
			<tr>
				<th>Datum</th>
				<th>User</th>
				<th>Start</th>
				<th>Stop</th>
				<th>P.</th>
				<th>Dauer</th>

				<th style="width:60px">Act</th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ($times as $time): ?>

				<tr class="<?php echo 'individualEntry' . $time['User']['id']; ?>">
					<td><?php echo substr($time['Time']['start'], 0, 10); ?></td>
					<td><?php echo $this->Html->link(
							$time['User']['name'],
							[
								'controller' => 'times',
								'action' => 'index',
								'?' => ['user_id' => $time['User']['id']]
							]); ?></td>
					<td><?php echo substr($time['Time']['start'], 10, 6); ?></td>
					<td><?php echo substr($time['Time']['stop'], 10, 6); ?></td>
					<td><?php echo $time['Time']['break']; ?></td>
					<?php
					if (empty($time['Time']['stop'])) {
						$time['Time']['stop'] = date('Y-m-d H:i:s');
					}

					$start = strtotime($time['Time']['start']);
					$stop = strtotime($time['Time']['stop']);
					$diff = ($stop - $start) / 3600 - $time['Time']['break'];
					?>
					<td><?php echo round($diff, 2); ?></td>

					<td class="actions right"><?php
						if ($userid == $time['User']['id'] && strtotime($time['Time']['start']) - strtotime(
								'-4 days'
							) > 0
						) {
							echo $this->Html->link(
									'<i class="icon-pencil"></i>',
									'/times/edit/' . $time['Time']['id'],
									['class' => 'btn btn-mini', 'escape' => false]
								) . ' ';
							echo $this->Html->link(
								'<i class="icon-remove"></i>',
								'/times/delete/' . $time['Time']['id'],
								['class' => 'btn btn-mini', 'escape' => false],
								'Are you sure you want to delete id ' . $time['Time']['id']
							);
						}
						?>
					</td>
				</tr>
				<tr class="border <?php echo 'individualEntry' . $time['User']['id']; ?>">
					<td colspan=7><?php echo $this->Html->link(
							$time['Customer']['name'],
							'/times/index/customer_id:' . $time['Customer']['id']
						); ?>
						| <?php if (!empty($time['Time']['task'])) {
							echo '<b>' . $time['Time']['task'] . ': </b>';
						} ?> <?php echo substr(
							$time['Time']['note'],
							0,
							60
						);
						if (strlen($time['Time']['note']) > 60) {
							echo '...';
						} ?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>


<?php echo $this->Html->link('Export', ['action' => 'export'], ['class' => 'btn']); ?>

<?php // echo $this->element('stats'); ?>
