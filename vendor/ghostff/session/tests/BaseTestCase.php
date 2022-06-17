<?php declare(strict_types=1);

use Ghostff\Session\Drivers\SetGet;
use Ghostff\Session\Session;
use PHPUnit\Framework\TestCase;

class BaseTestCase extends TestCase
{
    protected string $session_path = __DIR__ . DIRECTORY_SEPARATOR . 'seasons' . DIRECTORY_SEPARATOR;

    public function setUp(): void
    {
        parent::setUp();
        $session  = new ReflectionClass(Session::class);
        define('DEFAULT_SEGMENT', $session->getConstant('DEFAULT_SEGMENT'));
        define('SESSION_INDEX', $session->getConstant('SESSION_INDEX'));
        define('FLASH_INDEX', $session->getConstant('FLASH_INDEX'));
    }

    public function test_that_session_id_can_be_set()
    {
        $session_id =  bin2hex(random_bytes(32));
        $session    = new Session(null, $session_id);

        $this->assertSame([session_id(), $session->id()], [$session_id, $session_id]);
    }

    public function test_that_rotate_changes_current_session_id()
    {
        $session_id =  bin2hex(random_bytes(32));
        $session    = new Session(null, $session_id);
        $session->rotate();

        $this->assertNotSame([session_id(), $session->id()], [$session_id, $session_id]);
    }

    public function test_session_set_and_get()
    {
        $session = new Session();
        $session->set('string', 'one');
        $session->set('int', 1);
        $session->set('float', 12.034);
        $session->set('bool', true);
        $session->set('array', ['one', 2, 3.0, false]);
        $session->set('null', null);
        $session->set('object', $stdcls = new \StdClass());

        $this->assertSame([
            $session->get('string'),
            $session->get('int'),
            $session->get('float'),
            $session->get('bool'),
            $session->get('array'),
            $session->get('null'),
            $session->get('object'),
        ], ['one', 1, 12.034, true, ['one', 2, 3.0, false], null, $stdcls]);
    }

    public function test_that_uncommitted_sessions_are_not_saved()
    {
        $session = new Session();
        $session->set('foo', 'foo value');

        $this->assertEmpty($_SESSION);
    }

    public function test_that_push_adds_a_value_to_the_bottom_of_specified_key_value()
    {
        $session = new Session();
        $session->push('name', '1st');
        $session->push('name', '2nd');
        $session->push('name', '3rd');
        $session->commit();

        $this->assertSame($_SESSION, [DEFAULT_SEGMENT => [SESSION_INDEX => ['name' => ['1st', '2nd', '3rd']]]]);
    }

    public function test_push_and_pop()
    {
        $session = new Session();
        $session->push('name', 1);
        $session->push('name', 2);
        $session->push('name', 3);

        $this->assertSame([
            $session->pop('name'),
            $session->pop('name', true),
            $session->get('name'),
        ], [3, 1, [2]]);
    }

    public function test_the_pop_without_back_argument_removes_the_top_values_of_specified_key_values()
    {
        $session = new Session();
        $session->push('name', '1st');
        $session->push('name', '2nd');
        $session->push('name', '3rd');
        $session->push('name', '4th');

        $session->pop('name');
        $session->pop('name');
        $session->commit();

        $this->assertSame($_SESSION, [DEFAULT_SEGMENT => [SESSION_INDEX => ['name' => ['1st', '2nd']]]]);
    }

    public function test_the_pop_with_back_argument_removes_the_bottom_values_of_specified_key_values()
    {
        $session = new Session();
        $session->push('name', '1st');
        $session->push('name', '2nd');
        $session->push('name', '3rd');
        $session->push('name', '4th');

        $session->pop('name', true);
        $session->pop('name', true);
        $session->commit();

        $this->assertSame($_SESSION, [DEFAULT_SEGMENT => [SESSION_INDEX => ['name' => ['3rd', '4th']]]]);
    }

    public function test_that_trying_to_get_a_value_of_non_existing_key_throws_an_exception()
    {
        $this->expectException(\RuntimeException::class);

        $session = new Session();
        $session->get('non-existing-key');
    }

    public function test_that_session_getOrDefault_returns_default_value_if_key_was_not_found()
    {
        $session = new Session();
        $session->set('existing-key', 111);

        $this->assertSame([
            $session->getOrDefault('non-existing-key'),
            $session->getOrDefault('non-existing-key', 'Bar'),
            $session->getOrDefault('existing-key')
        ], [
            null,
            'Bar',
            111
        ]);
    }

    public function test_that_del_can_remove_set_data_from_session()
    {
        $this->expectException(\RuntimeException::class);
        $session = new Session();
        $session->set('foo', 'Bar');
        $session->del('foo');

        $session->get('foo');
    }

    public function test_that_push_can_remove_set_data_from_session()
    {
        $this->expectException(\RuntimeException::class);
        $session = new Session();
        $session->push('name', '124');
        $session->del('name');

        $session->get('name');
    }

    public function test_that_commit_saves_session()
    {
        $session = new Session();
        $value   = 'foo value';
        $session->set('foo', $value);
        $session->commit();

        $this->assertSame($_SESSION, [DEFAULT_SEGMENT => [0 => ['foo' => $value]]]);
    }

    public function test_setFlash_and_getFlash()
    {
        $session = new Session();
        $session->setFlash('string', 'one');
        $session->setFlash('int', 1);
        $session->setFlash('float', 12.034);
        $session->setFlash('bool', true);
        $session->setFlash('array', ['one', 2, 3.0, false]);
        $session->setFlash('null', null);
        $session->setFlash('object', $stdcls = new \StdClass());

        $this->assertSame([
            $session->getFlash('string'),
            $session->getFlash('int'),
            $session->getFlash('float'),
            $session->getFlash('bool'),
            $session->getFlash('array'),
            $session->getFlash('null'),
            $session->getFlash('object'),
        ], ['one', 1, 12.034, true, ['one', 2, 3.0, false], null, $stdcls]);
    }

    public function test_that_trying_to_get_a_none_existing_flash_value_throws_a_RuntimeException()
    {
        $this->expectException(\RuntimeException::class);

        $session = new Session();
        $session->getFlash('non-existing-key');
    }

    public function test_that_flash_data_can_be_committed()
    {
        $session = new Session();
        $session->setFlash('first_name', 'Foo');
        $session->setFlash('last_name', 'Bar');
        $session->commit();

        $this->assertSame($_SESSION, [DEFAULT_SEGMENT => [FLASH_INDEX => ['first_name' => 'Foo', 'last_name' => 'Bar']]]);
    }

    public function test_that_data_store_as_flash_are_removed_after_it_has_been_read()
    {
        $this->expectException(\RuntimeException::class);
        $session = new Session();
        $session->setFlash('foo', 3434);
        $session->getFlash('foo');

        // This is not supposed to exist.
        $session->getFlash('foo');
    }

    public function test_that_uncommitted_flash_sessions_are_not_saved()
    {
        $session = new Session();
        $session->setFlash('foo', 'Hello');

        $this->assertEmpty($_SESSION);
    }

    public function test_that_flash_getOrDefault_returns_default_value_if_key_was_not_found()
    {
        $session = new Session();
        $session->setFlash('existing-key', 111);

        $this->assertSame([
            $session->getFlashOrDefault('non-existing-key'),
            $session->getFlashOrDefault('non-existing-key', 'Bar'),
            $session->getFlashOrDefault('existing-key')
        ], [null, 'Bar', 111]);
    }

    public function test_that_getAll_without_namespace_argument_returns_all_array_in_current_session_segment()
    {
        $session = new Session();
        $session->set('name', 'Foo');
        $session->push('ages', 10)->push('ages', 22);
        $session->setFlash('message', 'just a flash');
        $session->commit();

        $this->assertSame($_SESSION, [DEFAULT_SEGMENT => [
            SESSION_INDEX => ['name' => 'Foo', 'ages' => [10, 22]],
            FLASH_INDEX => ['message' => 'just a flash']
        ]]);
    }

    public function test_that_getAll_with_namespace_argument_returns_all_array_of_in_specified_segment()
    {
        $session = new Session();
        $segment = $session->segment('users');
        $segment->set('name', 'Foo');
        $segment->push('ages', 10)->push('ages', 22);
        $segment->setFlash('message', 'just a flash');
        $session->commit();

        $this->assertSame($_SESSION, ['users' => [
            SESSION_INDEX => ['name' => 'Foo', 'ages' => [10, 22]],
            FLASH_INDEX => ['message' => 'just a flash']
        ]]);
    }

    public function test_that_commit_cascades()
    {
        $session = new Session();
        $session->set('name', 'Bar');
        $session->setFlash('bar', 'Hello');

        $segment = $session->segment('test-segment');
        $segment->set('name', 'Foo');
        $segment->setFlash('bar', 'Imani');

        // This should commit session as well.
        $segment->commit();

        $this->assertSame($_SESSION, [
            DEFAULT_SEGMENT => [SESSION_INDEX => ['name' => 'Bar'], FLASH_INDEX => ['bar' => 'Hello']],
            'test-segment' => [SESSION_INDEX => ['name' => 'Foo'], FLASH_INDEX => ['bar' => 'Imani']]
        ]);
    }

    public function test_exist()
    {
        $session = new Session();
        $session->set('1st', 1);
        $session->push('2nd', 1);
        $session->setFlash('3rd', 2);
        $session->setFlash('4th', null);
        $session->set('5th', null);

        $this->assertSame([
            $session->exist('1st'),
            $session->exist('2nd'),
            $session->exist('3rd', true),
            $session->exist('4th', true),
            $session->exist('5th'),
            $session->exist('6th'),
            $session->exist('7th', true),
        ], [true, true, true, true, true, false, false]);
    }

    public function test_that_destroy_clear_all_session_data()
    {
        $session = new Session();
        $session->set('a', 'A')->push('b', 'B')->push('b', 'B')->setFlash('c', 'C');
        $session->segment('hello')->set('a', 'A')->push('b', 'B')->push('b', 'B')->setFlash('c', 'C');
        $session->commit();

        $expected[] = [
            DEFAULT_SEGMENT => [
                SESSION_INDEX => ['a' => 'A', 'b' => ['B', 'B']],
                FLASH_INDEX => ['c' => 'C']
            ], 'hello' => [
                SESSION_INDEX => ['a' => 'A', 'b' => ['B', 'B']],
                FLASH_INDEX => ['c' => 'C']
            ]
        ];
        $actual[] = $_SESSION;

        $session->destroy();

        $expected[] = [];
        $actual[] = $_SESSION;


        $this->assertSame($expected, $actual);
    }

    public function test_that_session_set_returns_the_current_session_instance()
    {
        $session = new Session();

        $this->assertInstanceOf(Session::class, $session->set('foo', false));
    }

    public function test_that_session_push_returns_the_current_session_instance()
    {
        $session = new Session();

        $this->assertInstanceOf(Session::class, $session->push('foo', 'fdfdf'));
    }

    public function test_that_session_del_returns_the_current_session_instance()
    {
        $session = new Session();

        $this->assertInstanceOf(Session::class, $session->del('foo'));
    }

    public function test_that_session_rotate_returns_the_current_session_instance()
    {
        $session = new Session();

        $this->assertInstanceOf(Session::class, $session->rotate());
    }

    public function test_that_session_clear_returns_the_current_session_instance()
    {
        $session = new Session();

        $this->assertInstanceOf(Session::class, $session->clear());
    }

    public function test_that_clear_clears_its_instance_data()
    {
        $session = new Session();
        $session->set('1st', '1')->set('2nd', '1');

        $bar_segment = $session->segment('bar');
        $bar_segment->set('1st', '2')->set('2nd', '2');

        $foo_segment = $session->segment('foo');
        $foo_segment->set('1st', '3')->set('2nd', '3');

        $foobar_segment = $session->segment('foobar');
        $foobar_segment->set('1st', '4')->set('2nd', '4');

        $user_segment = $session->segment('user');
        $user_segment->set('1st', '5')->set('2nd', '5');

        $bar_segment->clear();
        $foobar_segment->clear();
        $session->commit();

        $this->assertSame($_SESSION, [
            DEFAULT_SEGMENT => [SESSION_INDEX => ['1st' => '1', '2nd' => '1']],
            'bar' => [],
            'foo' => [SESSION_INDEX => ['1st' => '3', '2nd' => '3']],
            'foobar' => [],
            'user' => [SESSION_INDEX => ['1st' => '5', '2nd' => '5']],
        ]);
    }

    public function test_that_session_data_without_encrypt_config_attribute_is_not_encrypted()
    {
        $id      = 'non-encrypted-test-session-file-id';
        $session = new Session(null, $id);

        $session->set('name', 'FooBar')->commit();

        $session_raw_data = file_get_contents($this->session_path . "sess_${id}");
        unlink($this->session_path . "sess_${id}");

        $this->assertStringEndsWith(serialize([SESSION_INDEX => ['name' => 'FooBar']]), $session_raw_data);
    }

    public function test_that_session_data_with_encrypt_config_attribute_is_encrypted()
    {
        $id      = 'encrypted-test-session-file-id';
        $session = new Session($config = [
            Session::CONFIG_ENCRYPT_DATA => true,
            Session::CONFIG_SALT_KEY => $key = 'secret',
        ], $id);

        $session->set('name', 'FooBar')->commit();

        $set_get = new SetGet($config);

        $session_raw_data = file_get_contents($this->session_path . "sess_${id}");
        unlink($this->session_path . "sess_${id}");

        $this->assertStringEndsWith(serialize([SESSION_INDEX => ['name' => 'FooBar']]), $set_get->get($session_raw_data));
    }

    public function test_encrypted_data_can_be_set_and_get()
    {
        $session = new Session([Session::CONFIG_ENCRYPT_DATA => true]);
        $session->set('1', 'one')->push('2', 'two')->push('2', 'two1')->setFlash('3', 'three');

        $segment = $session->segment('seg');
        $segment->set('1', 'one')->push('2', 'two')->push('2', 'two1')->setFlash('3', 'three');
        $segment->commit();

        $expected[] = [
            DEFAULT_SEGMENT => [
                SESSION_INDEX => ['1' => 'one', '2' => ['two', 'two1']],
                FLASH_INDEX => ['3' => 'three']
            ], 'seg' => [
                SESSION_INDEX => ['1' => 'one', '2' => ['two', 'two1']],
                FLASH_INDEX => ['3' => 'three']
            ]
        ];
        $actual[] = $_SESSION;


        $expected[] = [
            'one',
            'two',
            'two1',
            'three',

            'one',
            'two1',
            'two',
            'three',
        ];
        $actual[] = [
            $session->get('1'),
            $session->pop('2', true),
            $session->pop('2'),
            $session->getFlash('3'),

            $segment->get('1'),
            $segment->pop('2'),
            $segment->pop('2', true),
            $segment->getFlash('3'),
        ];

        $this->assertSame($expected, $actual);
    }
}