<?php
use Admin\Model\Auth;
$identity = Auth::getIdentity();
if($identity['id'] == 1){
    return array(
        'default' => array(
//             array(
//                 'label' => '用户/权限',
//                 'module' => 'acl',
//                 'route' => 'default',
//                 'controller' => 'acl',
//                 'pages' => array(
//                     array(
//                         'label' => '用户',
//                         'route' => 'default',
//                         'controller' => 'acl',
//                         'action' => 'user-list',
//                         'resource' => 'acl_user-list',
//                         'link' => '/acl/user-list',
//                     ),
//                 ),
//            ),
            array(
                'label' => '用户管理',
                'route' => 'default',
                'module' => 'acl',
                'controller' => 'acl',
                'action' => 'user-list',
                'resource' => 'acl_acl_user-list',
                'link' => '/acl/user-list',
            ),
            
            array(
                'label' => '字典管理',
                'module' => 'dictionary',
                'route' => 'default',
                'controller' => 'dictionary',
                'pages' => array(
                    array(
                        'label' => '地区管理',
                        'route' => 'default',
                        'controller' => 'dictionary',
                        'action' => 'area',
                        'resource' => 'dictionary_dictionary_area',
                        'link' => '/dictionary/area',
                    ),
                    array(
                        'label' => '部门管理',
                        'route' => 'default',
                        'controller' => 'dictionary',
                        'action' => 'department',
                        'resource' => 'dictionary_dictionary_department',
                        'link' => '/dictionary/department',
                    ),
                    array(
                        'label' => '职位管理',
                        'route' => 'default',
                        'controller' => 'dictionary',
                        'action' => 'job',
                        'resource' => 'dictionary_dictionary_job',
                        'link' => '/dictionary/job',
                    ),
                    array(
                        'label' => '项目管理',
                        'route' => 'default',
                        'controller' => 'dictionary',
                        'action' => 'project',
                        'resource' => 'dictionary_dictionary_project',
                        'link' => '/dictionary/project',
                    ),
                    array(
                        'label' => '分类管理',
                        'route' => 'default',
                        'controller' => 'dictionary',
                        'action' => 'type',
                        'resource' => 'dictionary_dictionary_type',
                        'link' => '/dictionary/type',
                    ),
                )
            ),
        ),
    );
}else{
    return array(
        'default' => array(
            array(
                'label' => '用户/权限',
                'module' => 'acl',
                'route' => 'default',
                'controller' => 'acl',
                'pages' => array(
                    array(
                        'label' => 'Module',
                        'route' => 'default',
                        'controller' => 'acl',
                        'action' => 'module-list',
                        'resource' => 'acl_module-list',
                        'link' => '/acl/module-list',
                        'pages' => array(
                            array(
                                'label' => 'Add Module',
                                'module' => 'acl',
                                'route' => 'default',
                                'controller' => 'acl',
                                'action'     => 'module-edit',
                                'resource' => 'acl_module-edit',
                            ),
                        )
                    ),
                    array(
                        'label' => 'Controller',
                        'route' => 'default',
                        'controller' => 'acl',
                        'action' => 'controller-list',
                        'resource' => 'acl_controller-list',
                        'link' => '/acl/controller-list',
                        'pages' => array(
                            array(
                                'label' => 'Add Controller',
                                'controller' => 'acl',
                                'action'     => 'controller-edit',
                                'resource' => 'acl_controller-edit',
                            ),
                        )
                    ),
                    array(
                        'label' => 'Action',
                        'route' => 'default',
                        'controller' => 'acl',
                        'action' => 'action-list',
                        'resource' => 'acl_action-list',
                        'link' => '/acl/action-list',
                        'pages' => array(
                            array(
                                'label' => 'Add Action',
                                'controller' => 'acl',
                                'action'     => 'action-edit',
                                'resource' => 'acl_action-edit',
                            ),
                        )
                    ),
                    array(
                        'label' => '用户',
                        'route' => 'default',
                        'controller' => 'acl',
                        'action' => 'user-list',
                        'resource' => 'acl_user-list',
                        'link' => '/acl/user-list',
                    ),
                    array(
                        'label' => '角色/权限',
                        'route' => 'default',
                        'controller' => 'acl',
                        'action' => 'role-list',
                        'resource' => 'acl_role-list',
                        'link' => '/acl/role-list',
                    ),
                ),
            ),
            
            array(
                'label' => '导出管理',
                'route' => 'default',
                'module' => 'export',
                'controller' => 'export',
                'action' => 'list',
                'resource' => 'export_export_list',
                'link' => '/export/list',
            ),
            
            array(
                'label' => '工时填报',
                'route' => 'default',
                'module' => 'work',
                'controller' => 'work',
                'action' => 'list',
                'resource' => 'work_work_list',
                'link' => '/work/list',
            ),
            
            array(
                'label' => '字典管理',
                'module' => 'dictionary',
                'route' => 'default',
                'controller' => 'dictionary',
                'pages' => array(
                    array(
                        'label' => '地区管理',
                        'route' => 'default',
                        'controller' => 'dictionary',
                        'action' => 'area',
                        'resource' => 'dictionary_dictionary_area',
                        'link' => '/dictionary/area',
                    ),
                    array(
                        'label' => '部门管理',
                        'route' => 'default',
                        'controller' => 'dictionary',
                        'action' => 'department',
                        'resource' => 'dictionary_dictionary_department',
                        'link' => '/dictionary/department',
                    ),
                    array(
                        'label' => '职位管理',
                        'route' => 'default',
                        'controller' => 'dictionary',
                        'action' => 'job',
                        'resource' => 'dictionary_dictionary_job',
                        'link' => '/dictionary/job',
                    ),
                    array(
                        'label' => '项目管理',
                        'route' => 'default',
                        'controller' => 'dictionary',
                        'action' => 'project',
                        'resource' => 'dictionary_dictionary_project',
                        'link' => '/dictionary/project',
                    ),
                    array(
                        'label' => '分类管理',
                        'route' => 'default',
                        'controller' => 'dictionary',
                        'action' => 'type',
                        'resource' => 'dictionary_dictionary_type',
                        'link' => '/dictionary/type',
                    ),
                )
            ),
        ),
    );
}

