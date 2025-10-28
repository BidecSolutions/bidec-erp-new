<?php
url('/');
url()->current();
Config::set('database.default', 'mysql');
DB::reconnect('mysql');
?>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="bhoechie-tab-container">
            <div class="bhoechie-tab-menu">
                <div class="list-group">
                    <div data-toggle="collapse" data-target="#MasterMenu" class="collapsed">
                        <a href="#" class="list-group-item list-group-item-collaps">Master Menu</a>
                    </div>
                    <div class="lineHeight">&nbsp;</div>
                    <div class="sub-menu collapse" id="MasterMenu">
                        <?php
                        $MasterMenuTitles = DB::Connection('mysql')->table('main_menu_title')->select(['title','id','title_id'])->where('main_menu_id','=','Store')->where('status','=','1')->where('menu_type','=','2')->get();
                        ?>
                        @foreach($MasterMenuTitles as $MasterMenuTitle)
                        <div data-toggle="collapse" data-target="#{{ $MasterMenuTitle->title_id }}" class="collapsed" style="margin-left: 10px;">
                            <a href="#" class="list-group-item list-group-item-collaps">{{ $MasterMenuTitle->title }}</a>
                        </div>
                        <div class="sub-menu collapse" id="{{ $MasterMenuTitle->title_id }}"  style="margin-left: 10px;">
                            <?php
                            $subMenu = DB::Connection('mysql')->table('menu')->select(['m_type','name','m_controller_name','m_main_title','id','m_parent_code'])->where('m_parent_code','=',$MasterMenuTitle->id)->get();
                            foreach($subMenu as $row1){
                                $makeUrl = url(''.$row1->m_controller_name.'');
                            if(url()->current() == $makeUrl){
                                ?>
                                <script>$('#<?php echo $row1->m_main_title?>').addClass("in");</script>
                                <script>$('#MasterMenu').addClass("in");</script>
                            <?php
                            }
                            ?>
                                <a href="<?php echo url(''.$row1->m_controller_name.'?pageType='.$row1->m_type.'&&parentCode='.$row1->m_parent_code.'')?>" class="list-group-item <?php if(url()->current() == $makeUrl){echo 'triangle-isosceles right';}?>">&nbsp;<?php echo $row1->name;?></a>
                                <?php

                            }
                            ?>
                        </div>
                        <div class="lineHeight">&nbsp;</div>
                        @endforeach
                    </div>
                    <?php
                    $MainMenuTitles = DB::Connection('mysql')->table('main_menu_title')->select(['title','id','title_id'])->where('main_menu_id','=','Store')->where('status','=','1')->where('menu_type','=','1')->get();
                    ?>
                    @foreach($MainMenuTitles as $MainMenuTitle)
                    <div data-toggle="collapse" data-target="#{{ $MainMenuTitle->title_id }}" class="collapsed">
                        <a href="#" class="list-group-item list-group-item-collaps">{{ $MainMenuTitle->title }}</a>
                    </div>
                    <div class="sub-menu collapse" id="{{ $MainMenuTitle->title_id }}">
                        <?php
                        $subMenu = DB::Connection('mysql')->table('menu')->select(['m_type','name','m_controller_name','m_main_title','id','m_parent_code'])->where('m_parent_code','=',$MainMenuTitle->id)->get();
                        foreach($subMenu as $row1){
                            $makeUrl = url(''.$row1->m_controller_name.'');
                        if(url()->current() == $makeUrl){
                            ?>
                            <script>$('#<?php echo $row1->m_main_title?>').addClass("in");</script>
                        <?php
                        }
                        ?>
                            <a href="<?php echo url(''.$row1->m_controller_name.'?pageType='.$row1->m_type.'&&parentCode='.$row1->m_parent_code.'')?>" class="list-group-item <?php if(url()->current() == $makeUrl){echo 'triangle-isosceles right';}?>">&nbsp;<?php echo $row1->name;?></a>
                            <?php

                        }
                        ?>
                    </div>
                    <div class="lineHeight">&nbsp;</div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>