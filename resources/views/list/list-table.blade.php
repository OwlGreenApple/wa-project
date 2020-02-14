<!-- tab -->
@if($lists->count() > 0)
  @foreach($lists as $rows)
    <div class="bg-dashboard cardlist row">
      <div class="col-lg-4 pad-fix col-card">
        <h5>{{$rows->label}}</h5>
        <div>Link From : activele.com/{{$rows->name}}&nbsp;&nbsp;<span class="icon-copy"></span></div>
        <div>Create On : {{Date("M d, Y", strtotime($rows->created_at))}}</div><!--Jan 23, 2020  -->
      </div>

      <div class="col-lg-3 pad-fix cardnumber">
        <div class="big-number">+100</div>
        <div class="contact">New Contacts</div>
      </div> 

      <div class="col-lg-3 pad-fix cardnumber">
        <div class="big-number">50</div>
        <div class="contact">Contacts</div>
      </div>

      <div class="col-lg-2 pad-fix col-button">
        <button type="button" class="btn btn-warning btn-sm"><span class="icon-eye"></span></button>
        <button type="button" class="btn btn-success btn-sm"><span class="icon-copy-text"></span></button>
        <button type="button" class="btn btn-danger btn-sm"><span class="icon-delete"></span></button>
      </div>
    </div> 
  @endforeach
@endif
<!-- end tab -->
        

