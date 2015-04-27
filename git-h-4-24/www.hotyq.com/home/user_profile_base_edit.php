<?php
	header("content-type: text/html; charset=utf-8");
	session_start();
	require_once ('../includes/common_home_inc.php');
	require_once ('../../common/userprofile.class.php');
	require_once ('../../common/album.class.php');
	$user = new user();
	$base = new base();
	$album = new album();
	$userprofile = new userprofile();
	//var_dump($user_info);
	//��ȡcookie�е�id �� type
	$userid = $user_info['id'];
	$usertype = $user_info['user_type'];
//	var_dump($info);
	$smarty -> assign('$user_info',$user_info);		

	//��ȡ�û�����
	$userlist =	$userprofile ->get_user_profile($userid);
	//�����
	$AGE_RANGE = $COMMON_CONFIG["AGE"]["RANGE"];
	for($i = $AGE_RANGE['begin'];$i <= $AGE_RANGE['end'];$i++){
		$agenum[] = $i;
	}
	$smarty -> assign('agenum',$agenum);
	$smarty -> assign('AGE_RANGE',$AGE_RANGE);
	//��Χ��
	$BUST_RANGE = $COMMON_CONFIG["BUST"]["RANGE"];
	for($i = $BUST_RANGE['begin'];$i <=$BUST_RANGE['end'];$i++){
		$bustnum[] = $i;
	}
	$smarty -> assign('bustnum',$bustnum);
	$smarty -> assign('BUST_RANGE',$BUST_RANGE);
	//��Χ
	$WAIST_RANGE = $COMMON_CONFIG["WAIST"]["RANGE"];
	for($i = $WAIST_RANGE['begin'];$i <= $WAIST_RANGE['end'];$i++){
		$waistnum[] = $i;
	}
	$smarty -> assign('waistnum',$waistnum);
	$smarty -> assign('WAIST_RANGE',$WAIST_RANGE);
	//��Χ
	$HIPS_RANGE = $COMMON_CONFIG["HIPS"]["RANGE"];
	for($i = $HIPS_RANGE['begin'];$i <= $HIPS_RANGE['end'];$i++){
		$hipsnum[] = $i;
	}
	$smarty -> assign('hipsnum',$hipsnum);
	$smarty -> assign('HIPS_RANGE',$HIPS_RANGE);
	//���
	$HEIGHT_RANGE = $COMMON_CONFIG["HEIGHT"]["RANGE"];
	for($i = $HEIGHT_RANGE['begin'];$i <= $HEIGHT_RANGE['end'];$i++){
		$heightnum[] = $i;
	}	
	$smarty -> assign('heightnum',$heightnum);
	$smarty -> assign('HEIGHT_RANGE',$HEIGHT_RANGE);
	//��ҵ���
	$FINISH_YEAR = $COMMON_CONFIG["FINISH_YEAR"];
	for($i = date('Y');$i >= $FINISH_YEAR['min'] ;$i--){
		$yearnum[] = $i;
	}
	$smarty -> assign('yearnum',$yearnum);	
	$smarty -> assign('FINISH_YEAR',$FINISH_YEAR);	
	//����
	$WEIGHT_RANGE = $COMMON_CONFIG["WEIGHT"]["RANGE"];
	for($i = $WEIGHT_RANGE['begin'];$i <= $WEIGHT_RANGE['end'];$i++){
		$weightnum[] = $i;
	}			
	$smarty -> assign('weightnum',$weightnum);							
	$smarty -> assign('WEIGHT_RANGE',$WEIGHT_RANGE);
	//�û�״̬
	$STATE = $COMMON_CONFIG["STATE"];
	$smarty -> assign('STATE',$STATE);			
	//����
	$star_list = $COMMON_CONFIG["STAR"];
	$smarty -> assign('star_list',$star_list);	
	//Ѫ��
	$blood_list = $COMMON_CONFIG["BLOOD"];
	$smarty -> assign('blood_list',$blood_list);	
	//ѧ��
	$degree_list = $COMMON_CONFIG["DEGREE"];			
	$smarty -> assign('degree_list',$degree_list);	
	//��ȡuser������
	#��ȡnickname ��
	//�������ֵ
	//var_dump($user_info);


	//ȡ��ʡ������ֵ��ʾ
	$province_card = $base -> get_province_info($userlist['province_id'],$flash);	
	$province_c = $province_card['pname'];
	$city_card = $base -> get_city_info($userlist['city_id'],$flash);	
	$city_c = $city_card['cname'];	
	$district_card = $base -> get_district_info($userlist['district_id'],$flash);
	$district_c = $district_card['dname'];	

	$province_file = $base -> get_province_info($userlist['native_province_id'],$flash);	
	$province_p = $province_file['pname'];
	$city_file = $base -> get_city_info($userlist['native_city_id'],$flash);	
	$city_p = $city_file['cname'];	
	$district_file = $base -> get_district_info($userlist['native_district_id'],$flash);
	$district_p = $district_file['dname'];	
	
	$smarty -> assign('province_c',$province_c);
	$smarty -> assign('city_c',$city_c);
	$smarty -> assign('district_c',$district_c);
	$smarty -> assign('province_p',$province_p);
	$smarty -> assign('city_p',$city_p);
	$smarty -> assign('district_p',$district_p);

	//�û�������Ϣ��Ϣ����ǰ̨
	$smarty -> assign('userlist',$userlist);	#userprofile�б�
	$smarty -> assign('user_info',$user_info);		#user�û��б�
	//��ȡʡ��
	$plist = $base -> get_province_list($flash);
   	//var_dump($userlist);
   	//var_dump($user_info);

	$smarty -> assign('provincelist',$plist);	#ʡ���б�




	$smarty -> display("home/user_profile_base_edit.html");
?>