<?php

namespace App\Enums\Auth;

use App\Enums\Traits\EnumToArray;

enum PermissionType: string
{
    use EnumToArray;

    case USER_ACCESS = 'user_access';
    case USER_MANAGE = 'user_manage';

    case ROOM_ACCESS = 'room_access';
    case ROOM_MANAGE = 'room_manage';

    case ORDER_ACCESS = 'order_access';
    case ORDER_MANAGE = 'order_manage';

    case ORDER_DETAIL_ACCESS = 'order_detail_access';

    case CATEGORY_ACCESS = 'category_access';
    case CATEGORY_MANAGE = 'category_manage';

    case KOMPONENT_ACCESS = 'komponent_access';
    case KOMPONENT_MANAGE = 'komponent_manage';

    case DEVICE_ACCESS = 'device_access';
    case DEVICE_MANAGE = 'device_manage';

    case MANUFACTURER_ACCESS = 'manufacturer_access';
    case MANUFACTURER_MANAGE = 'manufacturer_manage';

    case ORDERSTATE_ACCESS = 'orderstate_access';
    case ORDERSTATE_MANAGE = 'orderstate_manage';

    case COMMISSION_ACCESS = 'commission_access';
    case COMMISSION_MANAGE = 'commission_manage';

    case COMMISSION_KOMPONENT_ACCESS = 'commission_komponent_access';
    case COMMISSION_KOMPONENT_MANAGE = 'commission_komponent_manage';

    case COMMISSION_SERVICE_ACCESS = 'commission_service_access';
    case COMMISSION_SERVICE_MANAGE = 'commission_service_manage';

    case ORDER_PRODUCT_ACCESS = 'order_product_access';
    case ORDER_PRODUCT_MANAGE = 'order_product_manage';

    case PRODUCT_ACCESS ='product_access';
    case PRODUCT_MANAGE ='product_manage';

    case SERVICE_ACCESS ='service_access';
    case SERVICE_MANAGE = 'service_manage';

}
