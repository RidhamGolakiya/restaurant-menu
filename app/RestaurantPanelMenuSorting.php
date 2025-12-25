<?php

namespace App;

enum RestaurantPanelMenuSorting : int
{
    case BUSINESS_HOURS = 9;
    case TABLES = 2;
    case MENU_CATEGORY = 3;
    case MENU = 4;
    case CUSTOMER = 5;
    case RESERVATION = 6;
    case USERS = 8;
    case RESTAURANTS_DETAILS = 1;
    case SETTINGS = 10;
}
