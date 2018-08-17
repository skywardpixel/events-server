<?php


use Phinx\Seed\AbstractSeed;

class ParticipantSeeder extends AbstractSeed
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
                'name' => 'Kyle Yan',
                'email' => 'kyleyan@uw.edu',
                'event_id' => '1'
            ],            [
                'name' => 'Kyle Yan',
                'email' => 'kyleyan@uw.edu',
                'event_id' => '2'
            ],
            [
                'name' => 'Jerry Yan',
                'email' => 'jerry@micetek.com',
                'event_id' => '2'
            ]
        ];

        $table = $this->table('participants');
        $table->insert($data)->save();
    }

    public function getDependencies() {
        return [
            'EventSeeder'
        ];
    }
}
