<!-- Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header hide">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="editModalLabel">Edit user <details></details></h4>
      </div>
      <form action="{{ URL::to('/settings/modify') }}" method="post">
        <div class="modal-body">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <div class="box" style="margin-top:0;">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" value="@if (isset($user_info)) {{$user_info->username}} @endif">
            <label for="email">Email</label>
            <input type="text" name="email" id="email" value="@if (isset($user_info)) {{$user_info->email}} @endif">
            <label for="password">New Password</label>
            <input type="password" name="password" id="password">
            <label for="rpassword">Repeat password</label>
            <input type="password" name="rpassword" id="rpassword">
          </div>
        </div>
        <div class="modal-footer" style="padding: 0;">
          <button type="submit" class="button">
            Save changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
