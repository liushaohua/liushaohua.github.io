<?php
          //header("content-type: text/html; charset=utf-8");
          session_start();
          require_once('../includes/common_home_inc.php');    
        
          $user = new user;
          $base = new base;
          $userprofile = new userprofile;
          $apply = new apply;
          $recruit = new recruit;

          $service_list = $base -> get_service_list($flash=0);    #所有服务名字;        
          $userid = $user_info['id'];
          $usertype = $user_info['user_type'];
          //招募id
          @$rid = intval($_REQUEST['recruit']);
          if($rid < 0 || $rid == 0){
                     exit('请不要捣乱!');
          }
          $recruit_info = $recruit -> get_recruit_info($rid);
          //获取招募的服务(二级服务)
          $recruit_service_list = $apply -> get_recruit_service($rid);
          
          if($recruit_service_list){
                     foreach ($recruit_service_list as $k => $v) {
                                $service_2_id = $v['service_2_id'];
                                $apply_num = $v['apply_num'];
                                $service_2_name = $service_list[$service_2_id];
                                $e_service_id = $v['id'];
                                $list[$service_2_name]['service_2_id'] = $service_2_id;
                                $list[$service_2_name]['e_service_id'] = $e_service_id;
                                @$list[$service_2_name]['num'] = $apply_num;
                     }
                     $smarty -> assign('list',$list);

                     @$e_service = intval($_REQUEST['service']);
                     @$apply_sex = clear_gpq($_REQUEST['sex']);
                     @$apply_result = clear_gpq($_REQUEST['result']);
                     @$s_help = clear_gpq($_REQUEST['s_help']);
                     @$r_help = clear_gpq($_REQUEST['r_help']);
                     if(!$e_service){
                                $service_2_array = array_shift($list);
                                $e_service = $service_2_array['e_service_id'];       
                     }
                     $smarty -> assign('active_service_id',$e_service);                    

                     $apply_list = $apply -> get_apply_by_recruit_service($rid,$e_service,$userid);

                    if($apply_list){
                                foreach ($apply_list as $key => $value) {
                                           $uid = $value['uid'];
                                           $connect_result = $value['result'];
                                           $user_info = $user -> get_userinfo($uid);
                                           $user_type = $user_info['user_type'];
                                           if($user_type == 'user'){
                                                     $u_profile = $userprofile -> get_user_profile($uid);
                                                     $u_sex = $u_profile['sex'];
                                           }else{
                                                     $u_sex = '';
                                           }

                                     
                                          if($apply_sex){
                                                        if ($apply_sex == $u_sex) {
                                                            $value['u_sex'] = $u_sex;
                                                            $value['user_type'] = $user_type;
                                                            $u_face = $user_info['icon_server_url'].$user_info['icon_path_url'];
                                                            $value['u_face'] = $u_face;
                                                            $value['u_name'] = $user_info['nickname'];

                                                            //三级服务
                                                            $e_apply_id = $value['id'];
                                                            $apply_item = $apply -> get_item_service_by_e_apply_id($e_apply_id);
                                                            
                                                            foreach ($apply_item as $kk => $vv) {
                                                                $third_service_name[$value['id']][$kk] = $service_list[$vv['service_3_id']];
                                                            }       
                                                            
                                                            $value['third_service'] = $third_service_name;
                                                            $apply_info_list[] = $value;
                                                        }
                                                        $smarty -> assign('sex',$apply_sex);
                                                        
                                         }else if($apply_result){
                                                        if ($apply_result == $connect_result) {
                                                            $value['u_sex'] = $u_sex;
                                                            $value['user_type'] = $user_type;
                                                            $u_face = $user_info['icon_server_url'].$user_info['icon_path_url'];
                                                            $value['u_face'] = $u_face;
                                                            $value['u_name'] = $user_info['nickname'];

                                                            //三级服务
                                                            $e_apply_id = $value['id'];
                                                            $apply_item = $apply     -> get_item_service_by_e_apply_id($e_apply_id);
                                                            
                                                            foreach ($apply_item as $kk => $vv) {
                                                                @$third_service_name[$value['id']][$kk] = $service_list[$vv['service_3_id']];
                                                            }       
                                                            
                                                            $value['third_service'] = $third_service_name;
                                                            $apply_info_list[] = $value;
                                                            
                                                        }
                                                        $smarty -> assign('result',$apply_result);
                                                        
                                          }else{
                                                        $value['u_sex'] = $u_sex;
                                                        $value['user_type'] = $user_type;
                                                        $u_face = $user_info['icon_server_url'].$user_info['icon_path_url'];
                                                        $value['u_face'] = $u_face;
                                                        $value['u_name'] = $user_info['nickname'];

                                                        //三级服务
                                                        $e_apply_id = $value['id'];
                                                        $apply_item = $apply -> get_item_service_by_e_apply_id($e_apply_id);
                                                        
                                                        foreach ($apply_item as $kk => $vv) {
                                                            //$rr = $base -> get_service_info($vv['service_3_id']);
                                                            @$third_service_name[$value['id']][$kk] = $service_list[$vv['service_3_id']];
                                                        }       
                                                        
                                                        $value['third_service'] = $third_service_name;
                                                        $apply_info_list[] = $value;
                                                        if($s_help == 'all'){
                                                            $smarty -> assign('sex','all'); 
                                                        }else if($r_help == 'all'){
                                                            $smarty -> assign('result','all');
                                                        }             
                                          }    
                                }
                               
                                if (empty($apply_info_list) || !isset($apply_info_list)) {
                                        $apply_info_list = '';
                                        $page_div = '';
                                        $smarty -> assign('page_div',$page_div);
                                        $smarty -> assign('apply_info_list',$apply_info_list);
                                }else{
                                        //分页
                                        $data['total_rows'] = count($apply_info_list);
                                        $page = '';
                                        if($data['total_rows'] > 0){
                                                //1 根据当前页p 先显示每次的分页
                                                $data['list_rows'] = 2;
                                                $page = new page($data);
                                                $page_div = $page -> show_1();
                                                
                                                //2  再根据当前分页p  显示数据
                                                $total_page = ceil($data['total_rows']/$data['list_rows']);
                                                $_REQUEST['page'] = (isset($_REQUEST['page']) && $_REQUEST['page']>0) ? intval($_REQUEST['page']) : 1;
                                                $_REQUEST['page'] = ($_REQUEST['page'] > $total_page) ? $total_page : intval($_REQUEST['page']);
                                                
                                                $from_rows = ($_REQUEST['page']-1) * $data['list_rows'];

                                                $show_apply_list = array_slice($apply_info_list, $from_rows, 2);

                                                //$new_page = ($_REQUEST['page']-1) * 10;
                                                //$smarty -> assign('page_new',$new_page);
                                                $smarty -> assign('page_div',$page_div);    
                                          }else{
                                                 $page_div = '';
                                                 $smarty -> assign('page_div',$page_div);    
                                          }
                                          $smarty -> assign('apply_info_list',$show_apply_list);  
                                }
                                
                     }else{
                            $apply_info_list = '';
                            $page_div = '';
                            $smarty -> assign('page_div',$page_div);
                            $smarty -> assign('apply_info_list',$apply_info_list);
                     }

           }else{
                    $list = '';
                    $smarty -> assign('list',$list); 
                    $page_div = '';
                    $smarty -> assign('page_div',$page_div);   
                    $apply_info_list = '';
                    $smarty -> assign('apply_info_list',$apply_info_list);
           }          
           
                     
            $smarty -> assign('recruit_info',$recruit_info);
            $work_type = 'mywork_recruit'; 
            $smarty -> assign('work_type',$work_type);
            $smarty -> display("home/export_apply_test.html");