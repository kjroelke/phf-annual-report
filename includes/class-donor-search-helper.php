<?php
/**
 * Class: Donor Search Helper
 *
 * @package KJR_Dev
 */

namespace KJR_Dev;

/**
 * Handles the donor search
 */
class Donor_Search_Helper {
	/**
	 * Whether to show the message or not
	 *
	 * @var bool $can_show_message
	 */
	private bool $can_show_message;

	/**
	 * Whether the list is the employee giving society
	 *
	 * @var bool $is_employee_giving_society
	 */
	private bool $is_employee_giving_society;

	/**
	 * Whether the list is the 1894 society
	 *
	 * @var bool $is_eighteen_ninety_four_society
	 */
	private bool $is_eighteen_ninety_four_society;

	/**
	 * Donor_Search_Helper constructor.
	 */
	public function __construct() {
		$this->is_employee_giving_society      = get_page_template_slug( get_the_ID() ) === 'templates/donors-list-multi-column.php';
		$this->is_eighteen_ninety_four_society = strpos( get_permalink( get_the_ID() ), '1894' ) !== false;
	}

	/**
	 * Whether the message should be shown or not
	 *
	 * @return bool
	 */
	public function has_message(): bool {
		return $this->is_employee_giving_society || $this->is_eighteen_ninety_four_society;
	}

	/**
	 * The message to show
	 */
	public function the_message(): void {
		if ( $this->is_eighteen_ninety_four_society ) {
			echo 'Names marked “*” have passed. We honor these donors for their contributions.';
		} elseif ( $this->is_employee_giving_society ) {
			echo 'Names marked “+” are members of our Employee Giving Society. Names marked “◊” have passed. We honor these donors for their contributions.';
		}
	}
}
