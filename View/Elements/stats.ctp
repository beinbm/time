

<div class="row">
	<div class="span10">
		<table class="table table-striped table-bordered table-condensed">
			<thead>
			<tr>
				<th>User</th>
				<th>curr. Month</th>
				<th>last Month</th>
				<th>Year</th>
				<th>last Year</th>
				<th></th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ($statistics as $stat_user => $stat_value): ?>
				<tr>
					<td><?php echo $stat_user ?></td>
					<td><?php echo round($stat_value['Time']['this_month'], 2) ?></td>
					<td><?php echo round($stat_value['Time']['last_month'], 2) ?></td>
					<td><?php echo round($stat_value['Time']['year'], 2) ?></td>
					<td><?php echo round($stat_value['Time']['last_year'], 2) ?></td>
					<td><?php echo round($stat_value['Time']['life'] - $stat_value['Payment']['life_hours'], 2) ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>

<?php if ($groupid == 1) { ?>

	<?php
	foreach ($projectstatistics as $key => $stats) {
		foreach ($stats as $stat) {
			if (empty($stat['times']['task'])) {
				$stat['times']['task'] = ' ';
			}

			$stat_project[$stat['times']['customer_id']][$stat['times']['task']][$key] = round($stat[0]['sum'], 2);

		}
	}

	?>
	<div class="row">
		<div class="span7">
			<table class="table table-striped table-bordered table-condensed">
				<thead>
				<tr>
					<th>Customer</th>
					<th>Task</th>
					<th>akt. Monat</th>
					<th>vor. Monat</th>
					<th>Jahr</th>
					<th>Life</th>
				</tr>
				</thead>
				<tbody>
				<?php
				foreach ($stat_project as $cust_key => $stat_customer) {
					foreach ($stat_customer as $task_key => $stat_task) {

						if (!isset($stat_task['this_month'])) {
							$stat_task['this_month'] = '';
						}
						if (!isset($stat_task['last_month'])) {
							$stat_task['last_month'] = '';
						}
						if (!isset($stat_task['year'])) {
							$stat_task['year'] = '';
						}
						if (!isset($stat_task['life'])) {
							$stat_task['life'] = '';
						}
						?>
						<tr>
							<td><?php if (isset($customers[$cust_key])) {
									echo $this->Html->link(
										$customers[$cust_key],
										'/times/index/customer_id:' . $cust_key
									);
								} ?>
							</td>
							<td><?php echo $task_key; ?></td>
							<td><?php echo round($stat_task['this_month']); ?></td>
							<td><?php echo round($stat_task['last_month']); ?></td>
							<td><?php echo round($stat_task['year']); ?></td>
							<td><?php echo round($stat_task['life']); ?></td>
						</tr>
						<?php
					}
				}
				?>
				</tbody>
			</table>
		</div>

		<div class="span3">
			<table class="table table-striped table-bordered table-condensed">
				<thead>
				<tr>
					<th>Jahr</th>
					<th>Monat</th>
					<th>Stunden</th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($monthly_stats as $stats) { ?>
					<tr>
						<td><?php echo $stats[0]['year'] ?></td>
						<td><?php echo $stats[0]['month'] ?></td>
						<td><?php echo round($stats[0]['sum']); ?></td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
		</div>
	</div>

<?php } ?>
