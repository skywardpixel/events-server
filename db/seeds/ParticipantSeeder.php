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
                'email' => 'kyle@somewhere.com',
                'company' => 'Somewhere',
                'phone' => '1872229992',
                'event_id' => '1'
            ],
            [
                'name' => 'Kyle Yan',
                'email' => 'kyle@somewhere.com',
                'company' => 'Somewhere',
                'phone' => '1872229992',
                'event_id' => '2'
            ],
            [
                'name' => 'Jerry Yan',
                'email' => 'jerry@somewhere.com',
                'company' => 'Somewhere',
                'phone' => '2012829999',
                'event_id' => '1'
            ],
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
