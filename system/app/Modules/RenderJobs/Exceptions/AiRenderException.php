<?php

namespace App\Modules\RenderJobs\Exceptions;

use RuntimeException;

/**
 * Thrown when an AI provider call for a render job fails. The message is
 * always safe to show to the end user (and to store as the job's
 * error_message) — the original exception should be logged separately by
 * the caller before this is thrown.
 */
class AiRenderException extends RuntimeException {}
