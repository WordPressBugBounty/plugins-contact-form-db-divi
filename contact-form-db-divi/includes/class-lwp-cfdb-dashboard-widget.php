<?php
/**
 * Adds a Contact Form DB Divi dashboard widget in WordPress admin.
 *
 * @package Contact_Form_DB_Divi
 * @since   1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class for rendering the Contact Form DB Divi dashboard widget.
 *
 * @since 1.4.0
 */
class Lwp_Cfdb_Dashboard_Widget {

	// ===========================================================================================

	/**
	 * Registers hooks for the dashboard widget.
	 *
	 * @since 1.4.0
	 */
	public function __construct() {
		add_action( 'wp_dashboard_setup', array( $this, 'register_widget' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
	}

	// ===========================================================================================

	/**
	 * Registers the dashboard widget for users who can edit posts.
	 *
	 * @since 1.4.0
	 * @return void
	 */
	public function register_widget() {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		wp_add_dashboard_widget(
			'lwp_cfdb_dashboard_widget',
			esc_html__( 'Divi Form DB', 'contact-form-db-divi' ),
			array( $this, 'render_widget' )
		);
	}

	// ===========================================================================================

	/**
	 * Enqueues dashboard widget styles on the WordPress dashboard screen only.
	 *
	 * @since 1.4.0
	 * @return void
	 */
	public function enqueue_styles() {
		if ( ! function_exists( 'get_current_screen' ) ) {
			return;
		}

		$screen = get_current_screen();

		if ( ! $screen || 'dashboard' !== $screen->base ) {
			return;
		}

		wp_enqueue_style(
			'lwp-cfdb-dashboard-widget',
			LWP_CFDB_PLUGIN_URL . 'assets/css/dashboard-widget.css',
			array( 'dashicons' ),
			LWP_CFDB_VERSION
		);
	}

	// ===========================================================================================

	/**
	 * Renders the dashboard widget output.
	 *
	 * @since 1.4.0
	 * @return void
	 */
	public function render_widget() {
		$submission_counts  = $this->get_submission_counts();
		$recent_submissions = $this->get_recent_submissions();
		$all_entries_url    = admin_url( 'edit.php?post_type=lwp_form_submission' );
		$documentation_url  = 'https://www.learnhowwp.com/documentation/contact-form-db-divi/';
		$video_url          = 'https://www.youtube.com/watch?v=02jkCpG1kXA';
		?>
		<div class="summary">
			<div class="summary-card summary-card--unread">
				<span class="dashicons dashicons-email-alt summary-icon" aria-hidden="true"></span>
				<div class="summary-content">
					<span class="summary-count"><?php echo esc_html( (string) $submission_counts['unread'] ); ?></span>
					<span class="summary-label"><?php esc_html_e( 'Unread Submissions', 'contact-form-db-divi' ); ?></span>
				</div>
			</div>
			<div class="summary-card summary-card--total">
				<span class="dashicons dashicons-archive summary-icon" aria-hidden="true"></span>
				<div class="summary-content">
					<span class="summary-count"><?php echo esc_html( (string) $submission_counts['total'] ); ?></span>
					<span class="summary-label"><?php esc_html_e( 'Total Submissions', 'contact-form-db-divi' ); ?></span>
				</div>
			</div>
		</div>

		<h3 class="section-title">Recent Submissions</h3>
		<table class="wp-list-table widefat striped">
			<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'Read', 'contact-form-db-divi' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Email', 'contact-form-db-divi' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Date Submitted', 'contact-form-db-divi' ); ?></th>
					<th scope="col"><?php esc_html_e( 'View', 'contact-form-db-divi' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( ! empty( $recent_submissions ) ) : ?>
					<?php foreach ( $recent_submissions as $submission ) : ?>
						<?php
						$view_url       = admin_url( 'post.php?post=' . absint( $submission->ID ) . '&action=edit' );
						$email          = $this->get_submission_email( $submission->ID );
						$date_submitted = $this->get_submission_date( $submission->ID );
						$is_unread      = $this->is_submission_unread( $submission->ID );
						?>
						<tr>
							<td>
								<?php if ( $is_unread ) : ?>
									<span class="dashicons dashicons-email-alt" aria-hidden="true"></span>
								<?php else : ?>
									<span class="dashicons dashicons-yes-alt" aria-hidden="true"></span>
								<?php endif; ?>
							</td>
							<td><?php echo esc_html( $email ); ?></td>
							<td><?php echo esc_html( $date_submitted ); ?></td>
							<td><a href="<?php echo esc_url( $view_url ); ?>"><?php esc_html_e( 'View', 'contact-form-db-divi' ); ?></a></td>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr>
						<td colspan="4"><?php esc_html_e( 'No form submissions found yet.', 'contact-form-db-divi' ); ?></td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>

		<p>
			<a class="button button-primary" href="<?php echo esc_url( $all_entries_url ); ?>">
				<?php esc_html_e( 'View All Submissions', 'contact-form-db-divi' ); ?>
			</a>
		</p>

		<div class="resources">
			<span class="resources-label"><?php esc_html_e( 'Questions?', 'contact-form-db-divi' ); ?></span>
			<a class="resource-link" href="<?php echo esc_url( $documentation_url ); ?>" target="_blank" rel="noopener noreferrer">
				<?php esc_html_e( 'View Documentation', 'contact-form-db-divi' ); ?>
				<span class="dashicons dashicons-external" aria-hidden="true"></span>
			</a>
			<a class="resource-link" href="<?php echo esc_url( $video_url ); ?>" target="_blank" rel="noopener noreferrer">
				<?php esc_html_e( 'Quick Setup Video', 'contact-form-db-divi' ); ?>
				<span class="dashicons dashicons-external" aria-hidden="true"></span>
			</a>
		</div>

		<?php if ( $this->is_free_version() ) : ?>
			<?php $upgrade_url = $this->get_upgrade_url(); ?>
			<?php if ( ! empty( $upgrade_url ) ) : ?>
				<p class="upgrade">
					<?php echo esc_html__( 'Upgrade to Pro version to export form submissions and view all fields saved by the form.', 'contact-form-db-divi' ); ?>
					<a class="upgrade-link" href="<?php echo esc_url( $upgrade_url ); ?>" target="_blank" rel="noopener noreferrer">
						<?php esc_html_e( 'Upgrade now', 'contact-form-db-divi' ); ?>
					</a>
				</p>
			<?php endif; ?>
		<?php endif; ?>

		<?php
	}

	// ===========================================================================================

	/**
	 * Gets total and unread submission counts.
	 *
	 * @since 1.4.0
	 * @return array<string, int> Submission count data.
	 */
	protected function get_submission_counts() {
		$submission_counts = wp_count_posts( 'lwp_form_submission' );

		$total_submissions = 0;
		if ( isset( $submission_counts->publish ) ) {
			$total_submissions = absint( $submission_counts->publish );
		}

		$unread_query = new WP_Query(
			array(
				'post_type'      => 'lwp_form_submission',
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Required to count unread submissions by plugin read-status meta.
				'meta_query'     => array(
					array(
						'key'     => 'lwp_cfdb_read_status',
						'value'   => false,
						'compare' => '=',
					),
				),
			)
		);

		$unread_submissions = absint( $unread_query->found_posts );

		return array(
			'total'  => $total_submissions,
			'unread' => $unread_submissions,
		);
	}

	// ===========================================================================================

	/**
	 * Gets the latest submissions for the dashboard table.
	 *
	 * @since 1.4.0
	 * @return WP_Post[]
	 */
	protected function get_recent_submissions() {
		$recent_submissions = get_posts(
			array(
				'post_type'      => 'lwp_form_submission',
				'post_status'    => 'publish',
				'posts_per_page' => 3,
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		);

		if ( is_array( $recent_submissions ) ) {
			return $recent_submissions;
		}

		return array();
	}

	// ===========================================================================================

	/**
	 * Gets the submission email from submission meta.
	 *
	 * @since 1.4.0
	 * @param int $submission_id Submission post ID.
	 * @return string
	 */
	protected function get_submission_email( $submission_id ) {
		$submission_details = get_post_meta( $submission_id, 'processed_fields_values', true );

		if ( isset( $submission_details['email']['value'] ) ) {
			return (string) $submission_details['email']['value'];
		}

		return __( 'Not available', 'contact-form-db-divi' );
	}

	// ===========================================================================================

	/**
	 * Gets the submission date from additional details.
	 *
	 * @since 1.4.0
	 * @param int $submission_id Submission post ID.
	 * @return string
	 */
	protected function get_submission_date( $submission_id ) {
		$additional_details = get_post_meta( $submission_id, 'additional_details', true );

		if ( isset( $additional_details['date_submitted'] ) ) {
			return (string) $additional_details['date_submitted'];
		}

		return get_the_date( 'Y-m-d H:i:s', $submission_id );
	}

	// ===========================================================================================

	/**
	 * Checks whether a submission is unread.
	 *
	 * @since 1.4.0
	 * @param int $submission_id Submission post ID.
	 * @return bool
	 */
	protected function is_submission_unread( $submission_id ) {
		$read_status = get_post_meta( $submission_id, 'lwp_cfdb_read_status', true );

		return in_array( $read_status, array( false, '', 0, '0' ), true );
	}

	// ===========================================================================================

	/**
	 * Checks if the active plugin build is the free version.
	 *
	 * @since 1.4.0
	 * @return bool
	 */
	protected function is_free_version() {
		global $lwp_cfdb_is_free_version;

		if ( isset( $lwp_cfdb_is_free_version ) ) {
			return (bool) $lwp_cfdb_is_free_version;
		}

		if ( function_exists( 'lwp_cfdd_fs' ) ) {
			return ! lwp_cfdd_fs()->is__premium_only();
		}

		return true;
	}

	// ===========================================================================================

	/**
	 * Gets the upgrade URL from Freemius when available.
	 *
	 * @since 1.4.0
	 * @return string
	 */
	protected function get_upgrade_url() {
		if ( function_exists( 'lwp_cfdd_fs' ) ) {
			return (string) lwp_cfdd_fs()->get_upgrade_url();
		}

		return '';
	}

	// ===========================================================================================
}
