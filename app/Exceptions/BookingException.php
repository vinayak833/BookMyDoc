<?php

namespace App\Exceptions;

use RuntimeException;

/** Thrown when a slot cannot be booked or an appointment cannot be cancelled. */
class BookingException extends RuntimeException
{
}
