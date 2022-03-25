<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Collection;
use LaravelZero\Framework\Commands\Command;

class ForceCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'force';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'randomly see a quote from The Star Wars Series';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $str = self::en()->random();
        $this->info($str);
        $this->notify('Force!', $str);
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }

    private static function en()
    {
        /* These are the quotes of Star Wars */
        return Collection::make(explode(
            "\n",
            "May The Force Be With You.

The Force is Strong in my family.

My Father Has it.

I have it.

My Sister have it.

You have that power too.

I find your lack of faith disturbing.

The Jedi you are.

Don't Underestimate the power of The Dark Side.

The Force is Strong with you.

Hmm! Adventure. Hmmpf! Excitement. A Jedi craves not these things.

Hey R2 , What do you think ?

Peeeeee poooo peeee peeee peee pooooo tuiiiiiiiiii.

AAAAAAAhhhhhhhhhhhhh AAAAAAAAhhhhhhhhhhhhhhhhh -Chewbacca.

Good Relations with Wookies I have.

I've got a bad feeling about this.

Now, witness the power of fully operational battle station.

Light it up, fuzzball!

Search your feelings.

I'm a Jedi, as my father before me.

Only at the end do you realize the power of the Dark Side.

IT'S A TRAP!

Give yourself to the Dark Side.

Luminous being are we, not this crude matter.

There's been an awakening in the Force. Have you felt it ?

Your eyes can deceive you. Don't trust them.

Do. Or do not. There is no try.

Your focus determines your reality.

So this is how liberty dies... with thunderous applause.

Never tell me the odds.

This is a new day, a new beginning.

It's true. All of it. The Dark Side, the Jedi. They're real.

Nothing will stand in our way... I will finish what you started.

The Force, it's calling to you. Just let it in.

Hope is not lost today... it is found.

I was raised to do one thing... but I've got nothing to fight for.

I'm no one. - Rey

I have lived long enough to see the same eyes in different people. I see your eyes... I know your eyes!

So, who talks first? Do you talk first? - Poe

I know all about waiting. For my family. They’ll be back. Some day.

I am with the Resistance. This is what we look like. Some of us look different.

That one’s garbage! Garbage Will Do.!

It’s true. All of it. The Dark Side. The Jedi. They’re real.

Luke is a Jedi. You’re his father.

You will remove these restraints and leave this cell with the door open.

That’s not how The Force works!

I’m being torn apart. I want to be free of this pain.

Chewie, We're Home"
        ))->filter(function ($val) {
            return '' != $val;
        });
    }
}
