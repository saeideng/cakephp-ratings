<?php
/**
 * Copyright 2010, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2010, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
namespace Ratings\Test\TestCase\Model\Behavior;

use App\Model\Model;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;

/**
 * CakePHP Ratings Plugin
 *
 * Ratable behavior tests
 *
 * @package 	ratings
 * @subpackage 	ratings.tests.cases.behaviors
 */
class RatableBehaviorTest extends TestCase {

/**
 * Holds the instance of the model
 *
 * @var mixed
 */
	public $Articles = null;

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.ratings.ratings',
		'plugin.ratings.articles',
		'plugin.ratings.posts',
		'plugin.ratings.users'
	);

/**
 * startTest
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Articles = TableRegistry::get('Articles');
		$this->Posts = TableRegistry::get('Posts');

		//$this->loadFixtures('Rating');
	}

/**
 * endTest
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		unset($this->Articles);
		unset($this->Posts);
		TableRegistry::clear();
	}

/**
 * Testing calculation of the rating
 *
 * @return void
 */
	public function testCalculateRating() {
		$this->Articles->addBehavior('Ratings.Ratable', array());
		$result = $this->Articles->calculateRating(1);
		$this->assertEquals($result['rating'], '1.0000');

		$result = $this->Articles->calculateRating(1, false);
		$this->assertEquals($result, '1.0000');

		$result = $this->Articles->calculateRating(1, 'title');
		$this->assertEquals($result['title'], '1.0000');

		$result = $this->Articles->calculateRating(2);
		$this->assertEquals($result['rating'], '0');

		$data = array(
			'Rating' => array(
				'foreign_key' => '1',
				'model' => 'Article',
				'user_id' => '3',
				'value' => 2.5000));
		$rating = $this->Articles->Ratings->newEntity($data);
		$this->Articles->Ratings->save($rating);
		$result = $this->Articles->calculateRating(1);
		$this->assertEquals($result['rating'], '1.75000000');

		$this->expectException('InvalidArgumentException');
		$this->Articles->calculateRating(1, true, 'pow');
	}

/**
 * Testing update of the rating
 *
 * @return void
 */
	public function testIncrementRating() {
		$this->Posts->addBehavior('Ratings.Ratable', array());
		$result = $this->Posts->incrementRating(1, 1)->toArray();
		$this->assertEquals($result['rating'], '1.0000');
		$this->assertEquals($result['rating_count'], 2);
		$this->assertEquals($result['rating_sum'], 2);
	}

	public function testIncrementRatingCalc() {
		$this->Posts->addBehavior('Ratings.Ratable', array());
		$result = $this->Posts->incrementRating(1, 1, false);
		$this->assertEquals($result, '1.0000');
	}

	public function testIncrementRatingOtherField() {
		$this->Posts->addBehavior('Ratings.Ratable', array());
		$result = $this->Posts->incrementRating(1, 1, 'title')->toArray();
		$this->assertEquals($result['title'], '1.0000');
		$this->assertEquals($result['rating_count'], 2);
		$this->assertEquals($result['rating_sum'], 2);
	}

	public function testIncrementRatingCalc2() {
		$this->Posts->addBehavior('Ratings.Ratable', array());
		$result = $this->Posts->incrementRating(2, 1)->toArray();
		$this->assertEquals($result['rating'], '2');
	}

	public function testIncrementRatingNewRating() {
		$this->Posts->addBehavior('Ratings.Ratable', array());
		$data = array(
			'foreign_key' => '1',
			'model' => 'Post',
			'user_id' => '3',
			'value' => 2.5000);
		$rating = $this->Articles->Ratings->newEntity($data);
		$this->Articles->Ratings->save($rating);
		$result = $this->Posts->incrementRating(1, 2.5)->toArray();
		$this->assertEquals($result['rating'], '1.75000000');

		$this->expectException('InvalidArgumentException');
		$this->Posts->incrementRating(1, 1, true, 'pow');
	}




	public function testDecrementRating() {
		$this->Posts->addBehavior('Ratings.Ratable', array());
		$result = $this->Posts->decrementRating(1, 1);
		$this->assertEquals($result['rating'], '0.0000');
		$this->assertEquals($result['rating_count'], 0);
		$this->assertEquals($result['rating_sum'], 0);
	}

	public function testDecrementRatingCalc() {
		$this->Posts->addBehavior('Ratings.Ratable', array());
		$result = $this->Posts->decrementRating(1, 1, false);
		$this->assertEquals($result, '0.0000');
	}

	public function testDecrementRatingOtherField() {
		$this->Posts->addBehavior('Ratings.Ratable', array());
		$result = $this->Posts->decrementRating(1, 1, 'title');
		$this->assertEquals($result['title'], '0.0000');
		$this->assertEquals($result['rating_count'], 0);
		$this->assertEquals($result['rating_sum'], 0);
	}

	public function testDecrementRatingCalc2() {
		$this->Posts->addBehavior('Ratings.Ratable', array());
		$result = $this->Posts->decrementRating(2, 1);
		$this->assertEquals($result['rating'], '0');
	}

	public function testDecrementRatingNewRating() {
		$this->Posts->addBehavior('Ratings.Ratable', array());
		$data = array(
			'Rating' => array(
				'foreign_key' => '1',
				'model' => 'Post',
				'user_id' => '3',
				'value' => 2.5000));
		$rating = $this->Articles->Ratings->newEntity($data);
		$this->Articles->Ratings->save($rating);
		$result = $this->Posts->incrementRating(1, 2.5);
		$this->assertEquals($result['rating'], '1.75000000');

		$result = $this->Posts->decrementRating(1, 2.5);
		$this->assertEquals($result['rating'], '1.50000000');

		$this->expectException('InvalidArgumentException');
		$this->Posts->decrementRating(1, 1, true, 'pow');
	}

/**
 * testSaveRating
 *
 * @return void
 */
	public function testSaveRating() {
		$this->Articles->addBehavior('Ratings.Ratable', array());
		$userId = '2'; // floriank
		$result = $this->Articles->saveRating(1, $userId, 4);
		$this->assertInternalType('array', $result);
		$this->assertEquals($result['rating'], '2.5000');

		$userId = '1'; // phpnut
		$this->assertFalse($this->Articles->saveRating(1, $userId, 4));
	}

/**
 * testSaveRating
 *
 * @return void
 */
	public function testSaveRatingWithAdditionalFields() {
		$this->Posts->addBehavior('Ratings.Ratable', array());
		$userId = '2'; // floriank
		$result = $this->Posts->saveRating(1, $userId, 4)->toArray();
		$this->assertInternalType('array', $result);
		$this->assertEquals($result['rating'], '2.5000');
		$this->assertEquals($result['rating_count'], 2);
		$this->assertEquals($result['rating_sum'], 5);

		$userId = '1'; // phpnut
		$this->assertFalse($this->Posts->saveRating(1, $userId, 4));
	}

/**
 * testSaveRating
 *
 * @return void
 */
	public function testSaveUpdatedRating() {
		$this->Posts->addBehavior('Ratings.Ratable', array(
			'update' => true));
		$userId = '1'; // phpnut
		$result = $this->Posts->saveRating(1, $userId, 3)->toArray();

		$this->assertInternalType('array', $result);
		$this->assertEquals($result['rating'], '3');
		$this->assertEquals($result['rating_count'], 1);
		$this->assertEquals($result['rating_sum'], 3);
	}

	public function testSaveUpdatedRatingForNewRating() {
		$this->Posts->addBehavior('Ratings.Ratable', array(
			'update' => true));
		$userId = '1'; // phpnut
		$result = $this->Posts->saveRating(3, $userId, 5)->toArray();

		$this->assertInternalType('array', $result);
		$this->assertEquals($result['rating'], '5');
		$this->assertEquals($result['rating_count'], 1);
		$this->assertEquals($result['rating_sum'], 5);
	}

/**
 * testSaveRating
 *
 * @return void
 */
	public function testRemoveRating() {
		$this->Articles->addBehavior('Ratings.Ratable', array());
		$userId = '2'; // floriank
		$result = $this->Articles->saveRating(1, $userId, 4);
		$this->assertInternalType('array', $result);
		$this->assertEquals($result['rating'], '2.5000');

		$result = $this->Articles->removeRating(1, $userId);
		$this->assertInternalType('array', $result);
		$this->assertEquals($result['rating'], '1.0000');

		$userId = '1'; // phpnut
		$this->assertFalse($this->Articles->saveRating(1, $userId, 4));
	}

/**
 * testSaveRating
 *
 * @return void
 */
	public function testRemoveRatingWithAdditionalFields() {
		$this->Posts->addBehavior('Ratings.Ratable', array());
		$userId = '2'; // floriank
		$result = $this->Posts->saveRating(1, $userId, 4)->toArray();
		$this->assertInternalType('array', $result);
		$this->assertEquals($result['rating'], '2.5000');
		$this->assertEquals($result['rating_count'], 2);
		$this->assertEquals($result['rating_sum'], 5);

		$result = $this->Posts->removeRating(1, $userId)->toArray();
		$this->assertInternalType('array', $result);
		$this->assertEquals($result['rating'], '1.0000');
		$this->assertEquals($result['rating_count'], 1);
		$this->assertEquals($result['rating_sum'], 1);

		$userId = '5'; // somebody
		$this->assertFalse($this->Posts->removeRating(1, $userId));
	}

/**
 * testSaveRating
 *
 * @return void
 */
	public function testRemoveUpdatedRating() {
		$this->Posts->addBehavior('Ratings.Ratable', array(
			'update' => true));
		$userId = '1'; // phpnut
		$result = $this->Posts->saveRating(1, $userId, 3)->toArray();

		$this->assertInternalType('array', $result);
		$this->assertEquals($result['rating'], '3');
		$this->assertEquals($result['rating_count'], 1);
		$this->assertEquals($result['rating_sum'], 3);

		$result = $this->Posts->removeRating(1, $userId)->toArray();

		$this->assertInternalType('array', $result);
		$this->assertEquals($result['rating'], '0');
		$this->assertEquals($result['rating_count'], 0);
		$this->assertEquals($result['rating_sum'], 0);
	}

	public function testRemoveUpdatedRatingForNewRating() {
		$this->Posts->addBehavior('Ratings.Ratable', array(
			'update' => true));
		$userId = '1'; // phpnut
		$result = $this->Posts->saveRating(3, $userId, 5)->toArray();

		$this->assertInternalType('array', $result);
		$this->assertEquals($result['rating'], '5');
		$this->assertEquals($result['rating_count'], 1);
		$this->assertEquals($result['rating_sum'], 5);

		$result = $this->Posts->removeRating(3, $userId)->toArray();
		$this->assertInternalType('array', $result);
		$this->assertEquals($result['rating'], '0');
		$this->assertEquals($result['rating_count'], 0);
		$this->assertEquals($result['rating_sum'], 0);
	}

/**
 * Testings Ratable::isRatedBy()
 *
 */
	public function testIsRatedBy() {
		$this->Articles->addBehavior('Ratings.Ratable', array());
		$userId = 1; // phpnut
		$foreignKey = 1;
		$result = $this->Articles->isRatedBy($foreignKey, $userId);
		$this->assertEquals(array(
			'id' => '1',
			'user_id' => '1',
			'foreign_key' => '1',
			'model' => 'Articles',
			'value' => '1.0000',
			'created' => '2009-01-01 12:12:12',
			'modified' => '2009-01-01 12:12:12',
		), $result);

		$userId = '1'; // phpnut
		$foreignKey = array(1, 2);
		$result = $this->Articles->isRatedBy($foreignKey, $userId);
		$this->assertEquals($result, array(1));
	}

/**
 * Testings Ratable::rate()
 *
 */
	public function testRate() {
		$this->Articles->addBehavior('Ratings.Ratable', array());
		$userId = '3'; // phpnut
		$foreignKey = 1;
		$result = $this->Articles->rate($foreignKey, $userId, 'up');
		$this->assertTrue($result);

		$this->expectException('RuntimeException');
		$this->Articles->rate($foreignKey, $userId, 'up');

		$this->expectException('OutOfBoundsException');
		$this->Articles->rate('does-not-exist', $userId, 'up');

		$this->expectException('OutOfBoundsException');
		$this->Articles->rate($foreignKey, $userId, 'invalid-rating');

		$this->expectException('LogicException');
		$this->Articles->rate($foreignKey, 0, 'up');
	}

/**
 * Testings Ratable::cacheRatingStatistics()
 *
 */
	public function testCacheRatingStatistics() {
		$this->Articles->addBehavior('Ratings.Ratable', array());
		$this->Articles->saveRating(1, 4, 3);

		$data = array(
			'type' => 'saveRating',
			'foreignKey' => 1,
			'userId' => 4,
			'value' => 3,
			'update' => false,
			'oldRating' => false,
			'result' => array(
				'rating' => 2.00000000,
				'id' => 1));

		$result = $this->Articles->cacheRatingStatistics($data);
		$this->assertTrue(!empty($result));

		$this->Articles->recursive = -1;
		$result = $this->Articles->findById(1);
		$this->assertEquals($result['rating_3'], 1);
	}

	public function testCacheRatingStatisticsForRemove() {
		$this->Articles->addBehavior('Ratings.Ratable', array());
		$this->Articles->saveRating(1, 4, 3);

		$oldRating = $this->Articles->Ratings->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'Ratings.model' => 'Article',
				'Ratings.foreign_key' => 1,
				'Ratings.user_id' => 4)))->first();
		$result = $this->Articles->removeRating(1, 4);

		$data = array(
			'type' => 'saveRating',
			'foreignKey' => 1,
			'userId' => 4,
			'value' => 3,
			'update' => false,
			'oldRating' => false,
			'result' => array(
				'rating' => 2.00000000,
				'id' => 1));

		$result = $this->Articles->cacheRatingStatistics($data);

		$data = array(
			'type' => 'removeRating',
			'foreignKey' => 1,
			'userId' => 4,
			'value' => 3,
			'update' => false,
			'oldRating' => $oldRating,
			'result' => array(
				'rating' => 1.00000000,
				'id' => 1));

		$result = $this->Articles->cacheRatingStatistics($data);
		$this->assertTrue(!empty($result));

		$this->Articles->recursive = -1;
		$result = $this->Articles->findById(1);
		$this->assertEquals($result['rating_3'], 0);
	}
}
