
<div class="container align-middle ">
  <h2>Logs</h2>
  <table class="table table-condensed mt-5">
    <thead>
      <tr>
        <th>IP</th>
        <th>Last Log</th>
        <th>Number of Access</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach($logs as $log): ?>
      <tr>   
        <td><?= $log->indirizzo?></td>
        <td><?= $log->last_log?></td>
        <td><?= $log->login_count?></td>
      </tr>
      <?php endforeach; ?>
      <tr>
        
    </tbody>
  </table>
</div>