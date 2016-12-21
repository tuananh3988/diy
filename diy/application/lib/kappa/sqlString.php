<?PHP
	#投稿
	define('SQL_TAG_LIST',"         mtb_tag.id  tagID,
	                                mtb_tag.tag tagName");
	#投稿
	define('SQL_LIST_DATA',"        dtb_list.id             listID,
	                                dtb_list.type           listType,
	                                dtb_list.image          listImage,
	                                dtb_list.title          listTitle,
	                                dtb_list.text           listText,
	                                dtb_list.iine           listIineCount,
	                                dtb_list.comment_count  listCommentCount,
	                                FROM_UNIXTIME(dtb_list.created) listCreated");
	#投稿
	define('SQL_LIST_CMT_DATA',"    dtb_list_comment.id             listCommentID,
	                                dtb_list_comment.mtb_user_id    listCommentUserID,
	                                dtb_list_comment.text           listCommentText");
	#投稿
	define('SQL_LIST_ZAIRYO_DATA'," dtb_list_detail2.id      listZairyoID,
	                                dtb_list_detail2.title   listZairyoTitle,
	                                dtb_list_detail2.cnt     listZairyoCount");
	#投稿
	define('SQL_LIST_RESP_DATA',"   dtb_list_detail.id      listResipilID,
	                                dtb_list_detail.image   listResipiImage,
	                                dtb_list_detail.text    listResipiText");
    #ユーザー他人が見る時
	define('SQL_USER_DATA',"        mtb_user.id                 userID,
	                                mtb_user.name               userName,
	                                mtb_user.image              userImage,
	                                mtb_user.back_ground_image  userBackGroundImage");
    #ユーザー自分が見る時
	define('SQL_USER_DATA_MY',"     mtb_user.id                 userID,
	                                mtb_user.name               userName,
	                                mtb_user.hitokoto           userHitokoto,
	                                mtb_user.image              userImage,
	                                mtb_user.back_ground_image  userBackGroundImage,
	                                mtb_user.mail               userMail,
	                                mtb_user.tel                userTel,
	                                mtb_user.sex                userSex
	                                ");
	
        						
    #ユーザー自分が見る時
	define('SQL_INFOMATION',"       dtb_infomation.type            infomationType,
	                                dtb_infomation.dtb_list_id     infomationListID,
	                                dtb_infomation.target_user_id  infomationTargetUserID,
	                                dtb_infomation.created         infomationCreated
	                                ");
        						
        						
			            
?>