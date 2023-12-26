<?php
namespace Enums;

enum SQL: string
{
    case IS_OPERATOR = 'IS';
    case IS_NOT_OPERATOR = 'IS NOT';
    case IN_OPERATOR = 'IN';
    case NOT_IN_OPERATOR = 'NOT IN';
    case NULL = 'NULL';
}
