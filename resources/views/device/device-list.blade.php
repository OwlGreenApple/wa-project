@extends('layouts.app')

@section('content')

<!-- Modal Authorize -->
  <div class="modal fade child-modal" id="openAuthorize" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Authorize Your Phone</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">
            <div id="authorizescan"></div>
        </div>
      </div>
      
    </div>
  </div>

<!-- Modal Change Number -->
  <div class="modal fade child-modal" id="openChange" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Scan To Change Your Number</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">
            <div id="changenumberscan"></div>
        </div>
        <div class="modal-footer">
          If you want to change your number, please logout from whatsapp web on your device.<br/> After scan wait approximately <b>1 minutes</b> until it authorized. 
        </div>

      </div>
      
    </div>
  </div>

<!-- -->
<div class="container mb-2">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header"><b>My Devices</b></div>

                <div class="card-body">

                    <table class="table table-striped table-responsive" id="user-list">
                        <thead>
                            <th>No</th>
                            <th>Device Name</th>
                            <th>WA Number</th>
                            <th>Status</th>
                            <th>Authorize</th>
                            <th>Change Number</th>
                            <th>Delete Device</th>
                        </thead>
                        <tbody>
                            @if(count($data) > 0)
                                @php $no = 1; @endphp
                                @foreach($data['sender'] as $row)
                                 <tr>
                                    <td>{{$no}}</td>
                                    <td>{{$row->name}}</td>
                                    <td>{{$row->wa_number}}</td>
                                    <td>{{$data[$row->id]['status']}}</td>
                                    <td><a id="{{$row->device_id}}" class="btn btn-info btn-sm authorize">Authorize</a></td>
                                    <td><a id="{{$row->device_id}}" class="btn btn-warning btn-sm changenumber">Change Number</a></td>
                                    <td><a id="{{$row->device_id}}" class="btn btn-danger btn-sm deletedevice">Delete Device</a></td>
                                  </tr>
                                @php $no++; @endphp
                                @endforeach
                            @endif
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
<!-- end container -->
</div>


<script type="text/javascript">
    $(document).ready(function(){
        getScanBarcodeAuthorize();
        getScanBarcodeChangeNumber();
    });

    function getScanBarcodeAuthorize()
    {
        $(".authorize").click(function(){
            var device_id = $(this).attr('id');
            $("#openAuthorize").modal();

            $.ajax({
                beforeSend: function() {
                    $("#authorizescan").text('Loading....');
                },
                type : 'GET',
                url : '{{route("authorize")}}',
                data : {'id':device_id},
                dataType : 'html',
                success : function(result)
                {
                    $("#authorizescan").html(result);
                }
            })
        });

        /*
        var settings = {
          "async": true,
          "crossDomain": true,
          "url": "https://api.wassenger.com/v1/devices/5d6e15906de1a4001c90a0f4/scan?force=true",
          "method": "GET",
          "headers": {
            'Access-Control-Allow-Origin': '*'
          },
          data : {
            "token": "717c449cac6613abd70349cbd889b4955523292e7a45c49ebb2880b9b77e944d44f467389e75a080",
          },
          dataType : 'json',
          //jsonpCallback: "localJsonpCallback"
        }

        function localJsonpCallback(json)
        {
            $("#img").html(json);
        }

        $.ajax(settings).done(function (response) {
           console.log(response);
        });
        */
    }

    function getScanBarcodeChangeNumber()
    {
         $(".changenumber").click(function(){
            var device_id = $(this).attr('id');
            $("#openChange").modal();

            $.ajax({
                beforeSend: function() {
                    $("#changenumberscan").text('Loading....');
                },
                type : 'GET',
                url : '{{route("scan")}}',
                data : {'id':device_id},
                dataType : 'html',
                success : function(result)
                {
                    $("#changenumberscan").html(result);
                }
            })
        });
    }
</script>

@endsection
