<?php

namespace Mine\Interfaces\ServiceInterface;

use Mine\Interfaces\ServiceInterface\Resource\BaseResource;
use Mine\Interfaces\ServiceInterface\Resource\FieldValueResource;
use Mine\Interfaces\ServiceInterface\Resource\QueryResource;

interface QueryResourceServiceInterface extends BaseResource,QueryResource,FieldValueResource
{

}