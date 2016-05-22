<!-- Modal -->
<div class="modal fade" id="{{$source}}RemoveConfirm" tabindex="-1" role="dialog" aria-labelledby="{{$source}}RemoveConfirm" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header hide">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="{{$source}}RemoveConfirmLabel">Remove account confirmation</h4>
      </div>
      <form action="{{ URL::to('/remove/'.$source) }}" method="post">
        <div class="modal-body">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <div class="box" style="display: flex; justify-content: center;">
            <label>Are you sure you want to disconnect your <strong style="text-transform: capitalize; text-decoration:">{{$source}}</strong> account?</label>
            <!-- <label for="account">Account username</label>
            <input type="text" name="account" id="Account"> -->
            <!-- <label for="password">Password</label>
            <input type="password" name="password" id="password"> -->
          </div>
        </div>
        <div class="modal-footer" style="padding: 0;">
          <button type="submit" class="button error">
            Disconnect
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
