<!-- big if -->
@if($campaign->count() > 0)
   <!-- PAGINATION -->
  <div class="paging">
    {{ $paginate }}
  </div>
  
  @foreach($campaign as $row)
    <!-- BROADCAST -->
    @if($row->type == 2) 
      @php
        $broad_cast = $broadcast->where('campaign_id',$row->id)->first();
        $sending = Date('H:i',strtotime($broad_cast->hour_time));
        $day_send = Date('M d, Y',strtotime($broad_cast->day_send));
        $broadcast_message = $broad_cast->message;        

        $list_id = $row->list_id;
        $user_list = $userlist->find($list_id);
        
        if(!is_null($user_list))
        {
            $label = $user_list->label;
        }
        else 
        {
            $label = null;
        }

        $total_message = $campaign_controller->broadcastCampaign($row->id,'=',0)->count();
        $total_delivered = $campaign_controller->broadcastCampaign($row->id,'>',0)->count();
      @endphp

      <div class="bg-dashboard campaign row">
        <div class="col-lg-4 pad-fix col-card">
          <h5>{{ $row->name }}</h5>
          <div class="notes">
            <div>
              Type Campaign : <color><span class="gr">Broadcast
               @if($row->status == 0) -- draft @endif</span></color>
            </div>
            <div>
              Schedule post : <b>{{ $day_send }} {{ $sending }}</b>
            </div>
            @if($label !== null)
              <div>List : <a target="_blank" href="{{ url('list-edit') }}/{{ $list_id }}">{{ $label }}</a></div>
            @else
              <div><b>Deleted List</b></div>
            @endif
          </div>
          <div class="created">
            Created On : {{ Date('M d, Y',strtotime($row->created_at)) }}
          </div>
        </div>

        <div class="col-lg-5 pad-fix mt-4">
          <div class="row">
              @if($label !== null)
                <div class="col-lg-3 pad-fix cardnumber">
                  <div class="big-number">
                    <a class="contacts" href="{{url('list-campaign')}}/{{ $row->id }}/broadcast/1">{{ $total_message }}</a>
                  </div>
                  <div class="contact">Queue</div>
                </div>  
                <div class="col-lg-3 pad-fix cardnumber">
                  <div class="big-number">
                    <a class="contacts" href="{{url('list-campaign')}}/{{ $row->id }}/broadcast/0">{{ $total_delivered }}</a>
                  </div>
                  <div class="contact">Delivered</div>
                </div> 
              @else
                <div class="col-lg-3 pad-fix cardnumber">
                  <div class="big-number">
                    <div class="contacts">0</div>
                  </div>
                  <div class="contact">Queue</div>
                </div>  
                <div class="col-lg-3 pad-fix cardnumber">
                  <div class="big-number">
                    <div class="contacts">{{ $total_delivered }}</div>
                  </div>
                  <div class="contact">Delivered</div>
                </div> 
              @endif
              <!--
              <div class="col-lg-3 pad-fix cardnumber">
                <div class="big-number">9</div>
                <div class="contact">Opened</div>
              </div>
              <div class="col-lg-3 pad-fix cardnumber">
                <div class="big-number">9%</div>
                <div class="contact">Open Rate</div>
              </div>
              -->
          </div>  
        </div>

        <div class="col-lg-3 pad-fix col-button">
          @if($label !== null)
            @if($row->status == 1)
              <button id="{{ $broad_cast->id }}" type="button" class="btn btn-success broadcast_duplicate" data-toggle="tooltip" title="Button Duplicate"><span class="icon-copy-text"></span><span class="tooltipstered" title="Duplicate Campaign"></span></button>
            @endif
          @endif
            <button id="{{ $broad_cast->id }}" type="button" class="btn btn-danger broadcast-del" data-toggle="tooltip" data-placement="top" title="Button Delete"><span class="icon-delete"></span></button>
          @if($label !== null)
            <div>
              <a title="Edit Message" data-toggle="tooltip" id="{{ $broad_cast->id }}" data-name="{{ $row->name }}" data-date="{{ $broad_cast->day_send }}" data-message="{{ $broadcast_message }}" data-time="{{ $sending }}" data-publish="{{ $row->status }}" type="button" class="btn btn-custom edit_campaign">@if($row->status == 1)Edit @else Edit/ Publish @endif</a>
            </div>
          @endif
        </div>
        

      </div> 
      <!--end  broadcast -->
    @else
      <!-- AUTO SCHEDULE -->
        @php
          $reminder = $autoschedule->where([['campaign_id',$row->id],['is_event',0],['tmp_appt_id','=',0]])->join('lists','lists.id','=','reminders.list_id')->select('reminders.*','lists.label','lists.created_at')->first();

          $list_id = $row->list_id;
          $user_list = $userlist->find($list_id);
          
          if(!is_null($user_list))
          {
              $label = $user_list->label;
          }
          else 
          {
              $label = null;
          }

          $total_message = $campaign_controller->campaignsLogic($row->id,$userid,0,'=',0)->count(); 
          $total_delivered = $campaign_controller->campaignsLogic($row->id,$userid,0,'>',0)->count();
        @endphp

        @if(!is_null($reminder))
          @php
            $days = (int)$reminder->days;
            $total_template = $autoschedule->where('campaign_id',$row->id)->get()->count();
          @endphp

          <div class="bg-dashboard campaign row">
            <div class="col-lg-4 pad-fix col-card">
              <h5>{{ $row->name }}</h5>                                                
              <div class="notes">
                <div>Type Campaign : <color><span class="og">Auto schedule</span></color></div>
                @if($label !== null)
                  <div>List : <a target="_blank" href="{{ url('list-edit') }}/{{ $list_id }}">{{ $label }}</a></div>
                @else
                  <div><b>Deleted List</b></div>
                @endif
              </div>
              <div class="created">
                Created On : {{ Date('M d, Y',strtotime($row->created_at)) }}
              </div>
            </div>

            <div class="col-lg-5 pad-fix mt-4">
              <div class="row">
                 <!--  <div class="col-lg-3 pad-fix cardnumber">
                    <div class="big-number">100</div>
                    <div class="contact">Opened</div>
                  </div> -->
                  @if($label !== null)
                    <div class="col-lg-4 pad-fix cardnumber">
                      <div class="big-number">
                          <a class="contacts" href="{{url('add-message-auto-responder')}}/{{ $row->id}}">{{ $total_template }}</a>
                      </div>
                      <div class="contact">Total Template</div>
                    </div>

                    <div class="col-lg-3 pad-fix cardnumber">
                      <div class="big-number">
                          <a class="contacts" href="{{ url('list-campaign') }}/{{ $row->id }}/0/1">{{ $total_message }}</a>
                      </div>
                      <div class="contact">Queue</div>
                    </div>  
                    <div class="col-lg-3 pad-fix cardnumber">
                      <div class="big-number">
                          <a class="contacts" href="{{ url('list-campaign') }}/{{ $row->id }}/0/0">{{ $total_delivered }}</a>
                      </div>
                      <div class="contact">Delivered</div>
                    </div> 
                  @else
                     <div class="col-lg-4 pad-fix cardnumber">
                      <div class="big-number">
                          <div class="contacts">0</div>
                      </div>
                      <div class="contact">Total Template</div>
                    </div>

                    <div class="col-lg-3 pad-fix cardnumber">
                      <div class="big-number">
                          <div class="contacts">0</div>
                      </div>
                      <div class="contact">Queue</div>
                    </div>  
                    <div class="col-lg-3 pad-fix cardnumber">
                      <div class="big-number">
                          <div class="contacts">{{ $total_delivered }}</div>
                      </div>
                      <div class="contact">Delivered</div>
                    </div> 
                  @endif
                  
              </div>  
            </div>

            <div class="col-lg-3 pad-fix col-button">
               <button id="{{ $row->id }}" type="button" class="btn btn-danger responder-del" data-toggle="tooltip" data-placement="top" title="Button Delete"><span class="icon-delete"></span></button>
              @if($label !== null)
                <div>
                  <a href="{{ url('add-message-auto-responder') }}/{{ $row->id }}" class="btn btn-custom">Add / Edit</a>
                </div>
              @endif
              <!--
              <a href="{{url('report-reminder')}}" id="{{ $row['id'] }}" class="btn btn-warning btn-sm"><span class="icon-eye"></span></a>
              -->  
              <!-- <button id="{{ $row['id'] }}" type="button" class="btn btn-success btn-sm responder_duplicate"><span class="icon-copy-text"></span></button> -->
            </div>
        </div> 
        <!-- END AUTO SCHEDULE -->
        @else
          <div class="bg-dashboard campaign row">
            <div class="col-lg-4 pad-fix col-card">
              <h5>{{ $row->name }}</h5>                                                
              <div class="notes">
                <div>Type Campaign : <color><span class="og">Auto schedule</span></color></div>
                @if($label !== null)
                  <div>List : {{ $label }}</div>
                @else
                  <div><b>Deleted List</b></div>
                @endif
              </div>
              <div class="created">
                Created On : {{ Date('M d, Y',strtotime($row->created_at)) }}
              </div>
            </div>

            <div class="col-lg-5 pad-fix mt-4">
              <div class="row">
                  <div class="col-lg-4 pad-fix cardnumber">
                    <div class="big-number">
                        <a class="contacts" href="{{url('add-message-auto-responder')}}/{{ $row->id}}">0</a>
                    </div>
                    <div class="contact">Total Template</div>
                  </div>

                  <div class="col-lg-3 pad-fix cardnumber">
                    <div class="big-number">
                        <a class="contacts" href="{{ url('list-campaign') }}/{{ $row->id }}/0/1">0</a>
                    </div>
                    <div class="contact">Queue</div>
                  </div>  
                  <div class="col-lg-3 pad-fix cardnumber">
                    <div class="big-number">
                        <a class="contacts" href="{{ url('list-campaign') }}/{{ $row->id }}/0/0">0</a>
                    </div>
                    <div class="contact">Delivered</div>
                  </div> 
                  
              </div>  
            </div>

            <div class="col-lg-3 pad-fix col-button">
              <button id="{{ $row->id }}" type="button" class="btn btn-danger responder-del" data-toggle="tooltip" data-placement="top" title="Button Delete"><span class="icon-delete"></span></button>
              @if($label !== null)
              <div>
                <a href="{{ url('add-message-auto-responder') }}/{{ $row->id }}" class="btn btn-custom">Add / Edit</a>
              </div>
              @endif
            </div>
          </div> 
        <!-- END NULL REMINDER -->
        @endif
    <!-- END BIG IF  -->    
    @endif
  @endforeach

  <!-- PAGINATION -->
  <div class="paging">
    {{ $paginate }}
  </div>

@else
  <div class="alert alert-info">
    Sorry, the page you're currently page not available.
  </div>
@endif

<script type="text/javascript">
   $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip({
        'placement':'top'
      });   
   });
</script>