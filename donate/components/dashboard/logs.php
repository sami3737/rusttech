<?php

require(dirname(__FILE__) . '/../../require/classes.php');
$user = new User();
if (!$user->IsAdmin())
{
	die("You must be an admin to see this page.");
}

$sql = $dbcon->prepare("SELECT * FROM logs ORDER BY time DESC");
$sql->execute();
$results = $sql->fetchAll(PDO::FETCH_ASSOC);
array_walk_recursive($results, "escapeHTML");

?>

<div id="dashboard-content-container">
	<p id="dashboard-page-title">Logs</p>
	<div class="row">
	<div class="col-md-12">
		<div class="dashboard-stat-large">
			<div class="statistics-title">&nbsp;</div>
			<div class="statistics-content table-responsive">
				<table class="table">
					<thead>
						<tr>
							<th><?= getLangString("date") ?><button type="button" class="btn btn-default btn-sm tooltip-btn" data-toggle="tooltip" data-placement="top" title="The date and time at which this event took place.">?</button></th>
							<th>Error Type<button type="button" class="btn btn-default btn-sm tooltip-btn" data-toggle="tooltip" data-placement="top" title="The type of error that occured.">?</button></th>
							<th>Error Code<button type="button" class="btn btn-default btn-sm tooltip-btn" data-toggle="tooltip" data-placement="top" title="The asssociated error code (if applicable).">?</button></th>
							<th>Error Details<button type="button" class="btn btn-default btn-sm tooltip-btn" data-toggle="tooltip" data-placement="top" title="The details of the error.">?</button></th>
						</tr>
					</thead>
					<tbody>

					<?php
						if(count($results) > 0){
							foreach($results as $key => $value){
								print('
									<tr>
										<td>' . $value['time'] . '</td>
										<td>' . $value['errortype'] . '</td>
										<td>' . $value['errorcode'] . '</td>
										<td>' . $value['error'] . '</td>
									</tr>
								');
							}
						} else {
							print('<tr><td>There are no errors to show right now.</td></tr>');
						}
					?>

					</tbody>
				</table>
			</div>
		</div>
	</div>
	</div>
</div>
