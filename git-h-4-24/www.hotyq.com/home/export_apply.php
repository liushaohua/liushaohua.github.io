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

                     @$e_service = intval($_REQUEST['service']);
                     @$apply_sex = clear_gpq($_REQUEST['sex']);
                     @$apply_result = clear_gpq($_REQUEST['result']);
                     @$s_help = clear_gpq($_REQUEST['s_help']);
                     @$r_help = clear_gpq($_REQUEST['r_help']);
                     if(!$e_service){
                                $service_2_array = array_shift($list);
                                $e_service = $service_2_array['e_service_id'];       
                     }         

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
                                                          
                                          }    
                                }
                               
                                if (empty($apply_info_list) || !isset($apply_info_list)) {
                                        $apply_info_list = '';      
                                }
                     }else{
                            $apply_info_list = '';
                            $smarty -> assign('apply_info_list',$apply_info_list);
                     }

           }else{
                    $apply_info_list = '';
                    $smarty -> assign('apply_info_list',$apply_info_list);
           }          
           
            //导出报名信息
         if(!empty($_REQUEST['apply_export'])){
                  $name='报名信息列表.xls';    //生成的Excel文件文件名
                  Header( "Content-type:application/octet-stream");  
                  Header( "Accept-Ranges:bytes");  
                  Header( "Content-type:application/vnd.ms-excel");
                  header("Content-Disposition:filename=$name");
                  $htmlStr = '<table border="1">';
                  $htmlStr .= '<tr>';
                  $htmlStr .= '<td align="center">序号</td>';
                  $htmlStr .= '<td colspan=2 align="center">姓名</td>';
                  $htmlStr .= '<td colspan=2 align="center">性别</td>';   
                  $htmlStr .= '<td colspan=2 align="center">用户类型</td>'; 
                  $htmlStr .= '<td colspan=2 align="center">手机</td>';   
                  $htmlStr .= '<td colspan=2 align="center">邮箱</td>';     
                  $htmlStr .= '<td colspan=2 align="center">微信</td>';     
                  $htmlStr .= '<td colspan=2 align="center">QQ</td>';    
                  $htmlStr .= '<td colspan=2 align="center">报名时间</td>';    

                  $htmlStr .= '<td colspan=2 align="center">沟通结果</td>';     
                  $htmlStr .= '<td colspan=2 align="center">备注</td>';                
                  $htmlStr .= '</tr>';
                  
                  foreach($apply_info_list as $k=>$v)
                  {
                    $htmlStr .= '<tr>';
                    $htmlStr .= '<td align=center>'.($k+1).'</td>';
                    $htmlStr .= '<td colspan=2 align=center>'.$v['u_name'].'</td>';
                    $htmlStr .= '<td colspan=2 align=center>'.$v['u_sex'].'</td>';
                    $htmlStr .= '<td colspan=2 align=center>'.$v['user_type'].'</td>';
                    $htmlStr .= '<td colspan=2 align=center>'.$v['u_mobile'].'</td>';   
                    $htmlStr .= '<td colspan=2 align=center>'.$v['u_email'].'</td>';
                    $htmlStr .= '<td colspan=2 align=center>'.$v['u_weixin'].'</td>';
                    $htmlStr .= '<td colspan=2 align=center>'.$v['u_qq'].'</td>';
                    $htmlStr .= '<td colspan=2 align=center>'.$v['apply_date'].'</td>';
                    $htmlStr .= '<td colspan=2 align=center>'.$v['result'].'</td>';          
                    $htmlStr .= '<td colspan=2 align=center>'.$v['description'].'</td>';         
                    $htmlStr .= '</tr>';
                  }
                  $htmlStr .= '</table>'; 
                  echo $htmlStr;
                  exit;
          }          