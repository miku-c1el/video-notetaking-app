<?php 

namespace App\Enums;
 
enum VideoStatus: string
{
    case Active = 'active';
    case Deleted = 'deleted';
    case Expired = 'expired';
}

