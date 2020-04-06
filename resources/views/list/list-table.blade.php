<?php
  use App\Http\Controllers\ListController;
  $listcontroller = new ListController; 
?>

<!-- tab -->
@if($lists->count() > 0)
  @foreach($lists as $rows)
    <div class="bg-dashboard cardlist col-lg-12">

      <div class="row">
        <div class="col-lg-4 col-card">
          <h5>{{$rows->label}}</h5>
            <div class="link_wrap">Link From : <a href="{{env('APP_URL')}}{{$rows->name}}" target="_blank">{{env('APP_URL')}}{{$rows->name}}</a>
              <span>
                <a data-link="{{env('APP_URL')}}{{ $rows->name }}" class="btn-copy icon-copy"></a>
              </span>
            </div>

            <div>Created On : {{Date("M d, Y", strtotime($rows->created_at))}}</div> <!--Jan 23, 2020  -->
        </div>

        <div class="col-lg-3 pad-fix cardnumber">
          <div class="big-number">
            +<?php 
              echo $listcontroller->newContact($rows->id);
            ?>
          </div>
          <div class="contact">New Contacts</div>
        </div> 

        <div class="col-lg-3 pad-fix cardnumber">
          <div class="big-number"><?php 
              echo $listcontroller->contactList($rows->id);
            ?></div>
          <div class="contact">Contacts</div>
        </div>

        <div class="col-lg-2 pad-fix col-button">
          <button type="button" id="{{ $rows->id }}" class="btn btn-warning btn-sm open-table" data-toggle="tooltip" data-placement="top" title="Button Subscriber"><span class="icon-eye"></span></button>
          <a href="{{url('list-edit')}}/{{$rows->id}}" class="btn btn-edit btn-sm"><span class="icon-edit" data-toggle="tooltip" data-placement="top" title="Button Edit"></span></a>
          <a id="{{ $rows->id }}" class="btn btn-primary btn-sm open_export" data-toggle="tooltip" data-placement="top" title="Button Export"><span class="icon-export"></span></a>
          <button id="{{$rows->id}}" type="button" class="btn btn-success btn-sm duplicate" data-toggle="tooltip" data-placement="top" title="Button Duplicate" ><span class="icon-copy-text"></span></button>
          <a type="button" id="{{$rows->id}}" class="btn btn-danger btn-sm del" data-toggle="tooltip" data-placement="top" title="Button Delete"><span class="icon-delete"></span></a>
        </div> 
      </div>
    </div> 
  @endforeach
  <div class="paging">
    {{ $paginate }}
  </div>
  @else
  <div class="bg-dashboard cardlist row">
    Sorry, the page you're looking not available.
  </div>
@endif

<!-- end tab -->