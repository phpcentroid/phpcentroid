<?php

namespace PHPCentroid\Data;

enum DataObjectState: int
{
    case INSERT = 1;
    case UPDATE = 2;
    case DELETE = 4;
}