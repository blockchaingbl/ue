<ol class="breadcrumb">
  <li><a href="{{url('/')}}"> {{config("app.name")}}</a></li>
  @foreach ($site_map as $site_key=>$map_item)
  <li><a href="{{url($site_key)}}"><i class="iconfont">{{$map_item['icon']}}</i> {{$map_item['name']}}</a></li>
  @endforeach
</ol>