<div id="page-content" class="container ">
    <br>
    <div class="card">
        <style>
            h1{
                font-size: 26px;
            }
            #page-content > * {
                text-align: right;
            }
        </style>
        <div class="card-header">
            <h1>{{$page->title}}</h1>
        </div>
        <div class="card-body">
        <p>@php echo $page->text; @endphp</p>
        </div>
        <br>
    </div>
    <br>
    <br>
    <br>
    <br>
    <br>
</div>
