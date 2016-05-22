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
      <form action="{{ URL::to('/mycontent') }}" method="post">
        <div class="modal-body">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <div class="box" style="margin-top:0;">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" value="Iacob Ionut">
            <label for="email">Email</label>
            <input type="text" name="email" id="email" value="ionut.iacob17@gmail.com">
            <label for="avatar">Profile picture</label>
            <input type="file" name="avatar" id="avatar">
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
