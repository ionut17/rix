<!-- Modal -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header hide">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="addModalLabel">Attach API account</h4>
      </div>
      <form action="{{ URL::to('/authorize') }}" method="post">
        <div class="modal-body">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <div class="box" style="margin-top:0;">
            <label for="Api">Select API</label>
            <select name="api" id="api">
              <option value="github">Github</option>
              <option value="pocket">Pocket</option>
              <option value="slideshare" disabled>Slideshare</option>
              <option value="vimeo">Vimeo</option>
            </select>
            <!-- <label for="username">Username</label>
            <input type="text" name="username" id="username">
            <label for="password">Password</label>
            <input type="password" name="password" id="password"> -->
          </div>
        </div>
        <div class="modal-footer" style="padding: 0;">
          <button type="submit" class="button">
            Connect
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
