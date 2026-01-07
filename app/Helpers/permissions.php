<?php
 
    
    if (!defined('USER_ROLE_ADMIN')) define('USER_ROLE_ADMIN', 'admin');
    if (!defined('USER_ROLE_TEACHER')) define('USER_ROLE_TEACHER', 'teacher');
    if (!defined('USER_ROLE_STUDENT')) define('USER_ROLE_STUDENT', 'student');

    if (!defined('USER_ROLE_GUEST')) define('USER_ROLE_GUEST', 'guest');
    
    if(!defined('CAN_CREATE')) define('CAN_CREATE', 'create');
    if(!defined('CAN_READ')) define('CAN_READ', 'read');
    if(!defined('CAN_UPDATE')) define('CAN_UPDATE', 'update');
    if(!defined('CAN_DELETE')) define('CAN_DELETE', 'delete');
    if(!defined('CAN_MANAGE')) define('CAN_MANAGE', 'manage');

    if(!defined('PERMISSION_USER')) define('PERMISSION_USER', 'user');
    if(!defined('PERMISSION_ORGANIZATION')) define('PERMISSION_ORGANIZATION', 'organization');
    if(!defined('PERMISSION_CATEGORY')) define('PERMISSION_CATEGORY', 'category');
    if(!defined('PERMISSION_QUESTION')) define('PERMISSION_QUESTION', 'question');
    if(!defined('PERMISSION_QUESTION_SET')) define('PERMISSION_QUESTION_SET', 'question_set');
    if(!defined('PERMISSION_COURSE')) define('PERMISSION_COURSE', 'course');
    if(!defined('PERMISSION_COURSE_SECTION')) define('PERMISSION_COURSE_SECTION', 'course_section');
    if(!defined('PERMISSION_COURSE_MATERIAL')) define('PERMISSION_COURSE_MATERIAL', 'course_material');


    

    if(!defined('PERMISSION_FILE')) define('PERMISSION_FILE', 'file');
    if(!defined('PERMISSION_TRADE_NODE')) define('PERMISSION_TRADE_NODE', 'trade_node');
    if(!defined('PERMISSION_QUESTION_BANK')) define('PERMISSION_QUESTION_BANK', 'question_bank');
    if(!defined('PERMISSION_QUESTION_SET_META')) define('PERMISSION_QUESTION_SET_META', 'question_set_meta');
    if(!defined('PERMISSION_QUESTION_SET_QUESTION')) define('PERMISSION_QUESTION_SET_QUESTION', 'question_set_question');
    if(!defined('PERMISSION_QUESTION_SET_QUESTION_META')) define('PERMISSION_QUESTION_SET_QUESTION_META', 'question_set_question_meta');



    function permissionSetFunc(){
        return array(
            USER_ROLE_ADMIN => [
                PERMISSION_USER => [CAN_CREATE, CAN_READ, CAN_UPDATE, CAN_DELETE, CAN_MANAGE],
                PERMISSION_ORGANIZATION => [CAN_CREATE, CAN_READ, CAN_UPDATE, CAN_DELETE, CAN_MANAGE],
                PERMISSION_CATEGORY => [CAN_CREATE, CAN_READ, CAN_UPDATE, CAN_DELETE, CAN_MANAGE],
                PERMISSION_QUESTION => [CAN_CREATE, CAN_READ, CAN_UPDATE, CAN_DELETE, CAN_MANAGE],
                PERMISSION_QUESTION_SET => [CAN_CREATE, CAN_READ, CAN_UPDATE, CAN_DELETE, CAN_MANAGE],
                PERMISSION_QUESTION_BANK => [CAN_CREATE, CAN_READ, CAN_UPDATE, CAN_DELETE, CAN_MANAGE], 

            ],
            USER_ROLE_TEACHER => [
               // PERMISSION_USER => [CAN_READ, CAN_UPDATE, CAN_DELETE, CAN_MANAGE],
                PERMISSION_ORGANIZATION => [],
                PERMISSION_CATEGORY => [CAN_READ],    
                PERMISSION_QUESTION => [CAN_READ, CAN_UPDATE, CAN_DELETE, CAN_MANAGE],
                PERMISSION_QUESTION_SET => [CAN_READ, CAN_UPDATE, CAN_DELETE, CAN_MANAGE],
                //PERMISSION_COURSE => [CAN_READ, CAN_UPDATE, CAN_DELETE, CAN_MANAGE],
               // PERMISSION_COURSE_SECTION => [CAN_READ, CAN_UPDATE, CAN_DELETE, CAN_MANAGE],
                //PERMISSION_COURSE_MATERIAL => [CAN_READ, CAN_UPDATE, CAN_DELETE, CAN_MANAGE],
                //PERMISSION_FILE => [CAN_READ, CAN_UPDATE, CAN_DELETE, CAN_MANAGE],  
                //PERMISSION_TRADE_NODE => [CAN_READ, CAN_UPDATE, CAN_DELETE, CAN_MANAGE],
                PERMISSION_QUESTION_BANK => [CAN_READ], 
            ]
        );
    }

    function permissionSet(){
        return permissionSetFunc();
    }

    function permissionSetAdmin(){
        return permissionSetFunc()[USER_ROLE_ADMIN];
    }

    function permissionSetTeacher(){
        return permissionSetFunc()[USER_ROLE_TEACHER];
    }

    function getPermissionByRole($role){
        return permissionSetFunc()[$role];
    }
    function getRole(){;
        switch(auth()->user()->role){
            case USER_ROLE_ADMIN:
                return USER_ROLE_ADMIN;
            case USER_ROLE_TEACHER:
                return USER_ROLE_TEACHER;
            default:
                return USER_ROLE_STUDENT;
        }
    } 
    function getPermissionsApi(){
    $role = getRole();
    $data = getPermissionByRole($role);
    
    return apiResponse(true, 'Permissions', [
        'role' => $role,
        'permissions' => $data
    ]);
}



    


?>