 @if($appointments->count() > 0)

     <!-- PAGINATION -->
    <div class="paging">
      {{ $paginate }}
    </div>

    @foreach($appointments as $row)

      @php 
        $total_template = $templates->where('campaign_id',$row->id)->get()->count();
      @endphp

      <div class="bg-dashboard campaign row">
      <div class="col-lg-4 pad-fix col-card">
        <h5><span class="campaignid-{{$row->id}}">{{ $row->name }}</span>
            <span>
              <a data-name="{{ $row->name }}" id="{{ $row->id }}" class="edit icon-edit"></a>
            </span>  
        </h5>                           
        <div class="notes">
          <div>List : {{$row->label}}</div>
        </div>
        <div class="created">
          Create On : {{Date('d-M-Y',strtotime($row->created_at))}}
        </div>
      </div>

      <div class="col-lg-5 pad-fix mt-2">
        <div class="row"> 
            <div class="col-lg-4 pad-fix cardnumber">
                <div class="big-number">
                  <a class="contacts" href="{{url('edit-apt')}}/{{ $row->id }}">
                    {{ $total_template }}
                  </a>
                </div>
                <div class="contact">Total Template</div>
            </div>  
            <div class="col-lg-3 pad-fix cardnumber">
              <div class="big-number">
                <a class="contacts" href="{{ url('list-apt') }}/{{ $row->id }}/1">
                  {!! $logic_appointment->dataAppointment($row->id,'=',0)->count() !!}
                </a>
              </div>
              <div class="contact">Queue</div>
            </div> 
            <div class="col-lg-3 pad-fix cardnumber">
              <div class="big-number">
                <a class="contacts" href="{{ url('list-apt') }}/{{ $row->id }}/0">
                 {!! $logic_appointment->dataAppointment($row->id,'>',0)->count() !!}
                </a>
              </div>
              <div class="contact">Delivered</div>
            </div> 
            
        </div>  
      </div>

      <div class="col-lg-3 pad-fix col-button">
          <a class="btn btn-custom btn-sm text-white mt-0" href="{{ url('form-apt') }}/{{ $row->id }}">Create appointment</a>

          <a href="{{url('edit-apt')}}/{{ $row->id }}" data-toggle="tooltip" title="Add / Edit Appointment" class="btn btn-edit btn-sm"><span class="icon-edit"></span></a>

          <a href="{{url('export_csv_appt')}}/{{ $row->id }}" class="btn btn-success btn-sm" data-toggle="tooltip" title="Export XLSX"><span class="icon-export"></span></a>

          <button data-toggle="tooltip" title="Delete Appointment" type="button" id="{{ $row->id }}" class="btn btn-danger btn-sm appt-del"><span class="icon-delete"></span></button>
      </div>
  </div>   
  @endforeach

  <!-- PAGINATION -->
  <div class="paging">
    {{ $paginate }}
  </div>

 @else
  <h5 class="text-center">Currently you don't have any appointments, please click 'SETUP APPOINTMENT TEMPLATE'.
  </h5>
 @endif

 <script type="text/javascript">
   $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip({
        'placement':'top'
      });   
   });
</script>