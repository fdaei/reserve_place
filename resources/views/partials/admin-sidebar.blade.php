<!-- سایدبار -->
<?php
if (isset(explode("/",request()->path())[1]))
    $page=explode("/",request()->path())[1];
else
    $page="dashboard";

?>
<div class="sidebar" style="overflow-y: auto">

    <a href="{{url("admin/")}}" class="menu-item <?=$page=="dashboard"?"active":""?>">
        <i class="fas fa-chart-bar"></i>
        <span>داشبورد</span>
    </a>

    <a href="{{url("admin/users")}}" class="menu-item <?=$page=="users"?"active":""?>">
        <i class="fas fa-users-cog"></i>
        <span>مدیریت کاربران</span>
    </a>
    <a href="{{url("admin/message")}}" class="menu-item <?=$page=="message"?"active":""?>">
        <i class="fas fa-users-cog"></i>
        <span>پیام ها</span>
    </a>
    <a href="{{url("admin/supportAreas")}}" class="menu-item <?=$page=="supportAreas"?"active":""?>">
        &nbsp;
        &nbsp;
        &nbsp;
        <i class="fas fa-users-cog"></i>
        <span>دسته بندی پیام ها</span>
    </a>

    <a href="{{url("admin/provinces")}}"  class="menu-item <?=$page=="provinces"?"active":""?>">
        <i class="fas fa-map-marked-alt"></i>
        <span>مدیریت مناطق</span>
    </a>

    <a href="{{url("admin/residences")}}" class="menu-item <?=$page=="residences"?"active":""?>">
        <i class="fas fa-home"></i>
        <span>اقامتگاه‌ها</span>
    </a>

    <a href="{{url("admin/tools")}}" class="menu-item <?=$page=="tools"?"active":""?>">
        <i class="fas fa-tools"></i>
        <span>امکانات ویلاها</span>
    </a>
    <a href="{{url("admin/tools-foodstore")}}" class="menu-item <?=$page=="tools-foodstore"?"active":""?>">
        <i class="fas fa-tools"></i>
        <span>امکانات رستوران ها</span>
    </a>
    <a href="{{url("admin/tools-friends")}}" class="menu-item <?=$page=="tools-friends"?"active":""?>">
        <i class="fas fa-tools"></i>
        <span>آپشن های همسفران</span>
    </a>

    <a href="{{url("admin/comments")}}" class="menu-item <?=$page=="comments"?"active":""?>">
        <i class="fas fa-comment-dots"></i>
        <span>نظرات</span>
    </a>
    <a href="{{url("admin/pages")}}" class="menu-item <?=$page=="pages"?"active":""?>">
        <i class="fas fa-file-word-o"></i>
        <span>صفحات</span>
    </a>


    <a href="{{url("admin/website-settings")}}" class="menu-item <?=$page=="website-settings"?"active":""?>" >
        <i class="fas fa-cog"></i>
        <span>تنظیمات سایت</span>
    </a>

    <a href="{{url("admin/logout")}}" class="menu-item">
        <i class="fas fa-sign-out-alt"></i>
        <span>خروج</span>
    </a>
</div>
