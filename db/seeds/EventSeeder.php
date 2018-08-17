<?php


use Phinx\Seed\AbstractSeed;

class EventSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $data = [
            [
                'title' => 'Django Workshop',
                'location' => 'Shanghai',
                'description' => '#Django Workshop ' . "\n" . ' A _workshop_ on using the *Django* Framework',
                'date_time' => date('Y-m-d H:i:s'),
            ],            [
                'title' => 'Ruby On Rails Workshop',
                'location' => 'New York',
                'description' => 'A workshop on using the Rails Framework',
                'date_time' => date('Y-m-d H:i:s'),
            ]
        ];

        $table = $this->table('events');
        $table->insert($data)->save();
    }
}
