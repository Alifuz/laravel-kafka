<?php

namespace Aliftech\Kafka\Commit;

use Aliftech\Kafka\Commit\Contracts\Committer;

class VoidCommitter implements Committer
{
    public function commitMessage(): void
    {
    }

    public function commitDlq(): void
    {
    }
}
