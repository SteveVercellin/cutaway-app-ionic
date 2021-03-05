<?php

namespace BooklyAdapter\Classes;

use \BooklyAdapter\Entities\Appointment;
use \BooklyAdapter\Entities\Staff;
use \BooklyAdapter\Entities\Service;
use \BooklyAdapter\Entities\CustomerAppointment;
use \BooklyAdapter\Entities\Customer;

use \Bookly\Lib\Entities\Staff as BooklyStaff;
use \Bookly\Lib\Entities\Customer as BooklyCustomer;
use \Bookly\Lib\Entities\Appointment as BooklyAppointment;
use \Bookly\Lib\Entities\CustomerAppointment as BooklyCustomerAppointment;

class AppointmentGroup
{
    private $table = 'bookly_appointments_group';

    private static $_instance = null;

    private $statuses = array(
        '-2' => 'cancelled',
        '-1' => 'pending',
        '1' => 'approved'
    );

    private function __construct()
    {

    }

    public function getCustomerAppointments($customer, $status = 'pending', $offset = 0, $limit = CUSTOMER_APP_GROUPS_GET_LIMIT)
    {
        global $wpdb;

        $wpTable = $wpdb->prefix . $this->table;

        if (empty($customer)) {
            return array();
        }

        list($offset, $limit) = $this->validatePaginateData($offset, $limit);

		$appointments = array();
        $booklyAppAdapter = Appointment::getInstance();
        $booklyStaffTableName = BooklyStaff::getTableName();
        $appGroupDetail = new AppointmentGroupDetail();
        $appGroupDetailTableName = $appGroupDetail->getTableName();

        $select = $groupBy = "ag.id, ag.status, ag.group, staff.attachment_id";
        $select .= ", GROUP_CONCAT(agd.appointment_id) AS appointments";

        $from = "($wpTable AS ag INNER JOIN $booklyStaffTableName AS staff ON ag.staff_id = staff.id)";
        $from .= " INNER JOIN $appGroupDetailTableName AS agd ON ag.id = agd.appointment_group_id";

		$sql = $wpdb->prepare("SELECT $select FROM $from WHERE ag.customer_id = %d GROUP BY $groupBy", $customer);
		$order = '';
		if ( !empty( $status ) ) {
			$statusStr = $status;
			$status = $this->convertStatusStringToInt($status);
			$sql .= $wpdb->prepare(" AND `status` = %d", $status);
			if ($statusStr == 'pending') {
				$order = ' ORDER BY `created_date` DESC';
			} else {
				$order = ' ORDER BY `status`, `start_date`';
			}
		} else {
			$order = ' ORDER BY `start_date` DESC';
		}

		$sql .= $order;

        if ($limit > 0) {
            $sql .= $wpdb->prepare(' LIMIT %d, %d', $offset, $limit);
        }
        $items = $wpdb->get_results($sql);
        if (!empty($items)) {
            foreach ($items as $item) {
				$staffAttachmentId = $item->attachment_id;
                $status = $item->status;
                $group = $item->group;
                $apps = $item->appointments;
                $apps = explode( ',', $apps );
                if (!empty($apps)) {
                    $price = 0;
                    $startTime = '';
                    $staffName = '';
                    foreach ($apps as $app) {
                        $appDetail = $booklyAppAdapter->getAppointmentDetail($app);
                        if (!empty($appDetail)) {
                            $startTime = empty($startTime) ? $appDetail['start_date'] : $startTime;
                            $staffName = empty($staffName) ? $appDetail['barber_name'] : $staffName;
                            $price += $appDetail['service_price'];
                        }
                    }

                    if (!empty($startTime) && !empty($staffName) && !empty($price)) {
                        $startTime = preg_split('~\s+~', $startTime);

                        $appointments[] = array(
                            'group' => $group,
                            'barber' => array(
								'attachment_id' => $staffAttachmentId,
                                'full_name' => $staffName
                            ),
                            'services' => array(
                                'price' => $price
                            ),
                            'date' => $startTime[0],
                            'time' => $startTime[1],
							'status' => $status,
                        );
                    }
                }
            }
        }

        return $appointments;
    }

    public function onlyLoadCustomerAppointment($customer, $group)
    {
        global $wpdb;

        $wpTable = $wpdb->prefix . $this->table;

        if (empty($customer) || empty($group)) {
            return array();
        }

        $sql = $wpdb->prepare("SELECT * FROM $wpTable WHERE customer_id = %d AND `group` = %d", $customer, $group);
        $item = $wpdb->get_row($sql);

        $status = 'unknow';

        if (!empty($item)) {
            $status = !empty($this->statuses[$item->status]) ? $this->statuses[$item->status] : $status;
        }

        $item->status_string = $status;

        return $item;
    }

    public function getCustomerAppointment($customer, $group)
    {
        global $wpdb;

        $wpTable = $wpdb->prefix . $this->table;

        if (empty($customer) || empty($group)) {
            return array();
        }

        $appointment = array();
        $booklyAppAdapter = Appointment::getInstance();
        $booklyStaffAdapter = Staff::getInstance();
        $booklyServiceAdapter = Service::getInstance();
        $appGroupDetail = new AppointmentGroupDetail();
        $appGroupDetailTableName = $appGroupDetail->getTableName();

        $select = $groupBy = "ag.id, ag.group, ag.status, ag.number_people";
        $select .= ", GROUP_CONCAT(agd.appointment_id) AS appointments";

        $from = "$wpTable AS ag INNER JOIN $appGroupDetailTableName AS agd ON ag.id = agd.appointment_group_id";

        $sql = $wpdb->prepare("SELECT $select FROM $from WHERE ag.customer_id = %d AND `group` = %d GROUP BY $groupBy", $customer, $group);
        $item = $wpdb->get_row($sql);
        if (!empty($item)) {
            $group = $item->group;
            $status = $item->status;
            $numberPeople = $item->number_people;
            $apps = $item->appointments;
            $apps = explode( ',', $apps );
            if (!empty($apps)) {
                $statusIden = !empty($this->statuses[$status]) ? $this->statuses[$status] : '';
                if (!empty($statusIden)) {
                    $staff = array();
                    $services = array();
                    $notes = array();
                    $startTime = '';

                    foreach ($apps as $app) {
                        $appDetail = $booklyAppAdapter->getAppointment($app);
                        if (!empty($appDetail)) {
                            $startTime = empty($startTime) ? $appDetail['start_date'] : $startTime;
                            if (empty($staff)) {
                                $staff = $booklyStaffAdapter->getStaffsDetail($appDetail['staff_id']);
                                $staff = !empty($staff) ? $staff[0] : array();
                            }
                            $services[] = $appDetail['service_id'];

                            $note = $appDetail['internal_note'];
                            if ( !empty( $note ) ) {
                                $notes = array_merge( $notes, explode("\n", $note) );
                            }
                        }
                    }

                    if (!empty($startTime) && !empty($services) && !empty($staff)) {
                        $services = $booklyServiceAdapter->getServicesDetail($services);
                        if (!empty($services)) {
                            $startTime = preg_split('~\s+~', $startTime);

                            $appointment = array(
                                'barber' => $staff,
                                'services' => $services,
                                'date' => $startTime[0],
                                'time' => $startTime[1],
                                'group' => $group,
                                'status' => $statusIden,
                                'notes' => $notes,
                                'number_people' => $numberPeople
                            );
                        }
                    }
                }
            }
        }

        return $appointment;
    }

    public function getCustomerAppointmentStatus($customer, $group)
    {
        global $wpdb;

        $wpTable = $wpdb->prefix . $this->table;

        if (empty($customer) || empty($group)) {
            return array();
        }

        $sql = $wpdb->prepare("SELECT `status` FROM $wpTable WHERE customer_id = %d AND `group` = %d", $customer, $group);
        $item = $wpdb->get_row($sql);
        $status = 'unknow';

        if (!empty($item)) {
            $status = !empty($this->statuses[$item->status]) ? $this->statuses[$item->status] : $status;
        }

        return $status;
    }

    public function getCustomerAppointmentIds($customer, $group)
    {
        global $wpdb;

        $wpTable = $wpdb->prefix . $this->table;
        $appGroupDetail = new AppointmentGroupDetail();
        $appGroupDetailTableName = $appGroupDetail->getTableName();
        $apps = array();

        if (empty($customer) || empty($group)) {
            return $apps;
        }

        $select = $groupBy = "ag.id";
        $select .= ", GROUP_CONCAT(agd.appointment_id) AS appointments";

        $from = "$wpTable AS ag INNER JOIN $appGroupDetailTableName AS agd ON ag.id = agd.appointment_group_id";

        $sql = $wpdb->prepare("SELECT $select FROM $from WHERE ag.customer_id = %d AND `group` = %d GROUP BY $groupBy", $customer, $group);
        $item = $wpdb->get_row($sql);
        if (!empty($item)) {
            $apps = $item->appointments;
            $apps = explode( ',', $apps );
            $apps = !empty($apps) ? $apps : array();
        }

        return $apps;
    }

    public function getCustomerNumberPeople($customer, $group)
    {
        global $wpdb;

        $wpTable = $wpdb->prefix . $this->table;

        if (empty($customer) || empty($group)) {
            return 0;
        }

        $sql = $wpdb->prepare("SELECT number_people FROM $wpTable WHERE customer_id = %d AND `group` = %d", $customer, $group);
        return $wpdb->get_var($sql);
    }

    public function getCustomerAppointmentsOnDate($customer, $date)
    {
        global $wpdb;

        $wpTable = $wpdb->prefix . $this->table;

        if (empty($customer) || empty($date)) {
            return false;
        }
        if (empty($date['day']) || empty($date['month']) || empty($date['year'])) {
            return false;
        }

        $select = '`start_date`, `end_date`';
        $from = $wpTable;
        $where = array(
            $wpdb->prepare('customer_id = %d', $customer),
            $wpdb->prepare('DAY(start_date) = %d', $date['day']),
            $wpdb->prepare('MONTH(start_date) = %d', $date['month']),
            $wpdb->prepare('YEAR(start_date) = %d', $date['year'])
        );
        $where = implode(" AND ", $where);
        $order = 'start_date';
        $sql = "SELECT $select FROM $from WHERE $where ORDER BY $order";
        $appointments = $wpdb->get_results($sql, 'ARRAY_A');

        return !empty($appointments) ? $appointments : array();
    }

    public function getStaffAppointmentsOnDate($staff, $date)
    {
        global $wpdb;

        $wpTable = $wpdb->prefix . $this->table;

        if (empty($staff) || empty($date)) {
            return false;
        }
        if (empty($date['day']) || empty($date['month']) || empty($date['year'])) {
            return false;
        }

        $pendingStatusInt = $this->convertStatusStringToInt('pending');
        $approvedStatusInt = $this->convertStatusStringToInt('approved');

        $select = '`start_date`, `end_date`';
        $from = $wpTable;
        $where = array(
            $wpdb->prepare('staff_id = %d', $staff),
            $wpdb->prepare('status = %d OR status = %d', $pendingStatusInt, $approvedStatusInt),
            $wpdb->prepare('DAY(start_date) = %d', $date['day']),
            $wpdb->prepare('MONTH(start_date) = %d', $date['month']),
            $wpdb->prepare('YEAR(start_date) = %d', $date['year'])
        );
        $where = implode(" AND ", $where);
        $order = 'start_date';
        $sql = "SELECT $select FROM $from WHERE $where ORDER BY $order";
        $appointments = $wpdb->get_results($sql, 'ARRAY_A');

        return !empty($appointments) ? $appointments : array();
    }

    public function getListStaffIdsCustomerBooked($customer)
    {
        global $wpdb;

        $wpTable = $wpdb->prefix . $this->table;
        $staffs = array();

        if (empty($customer)) {
            return $staffs;
        }

        $sql = $wpdb->prepare("SELECT staff_id FROM $wpTable WHERE customer_id = %d", $customer);
        $items = $wpdb->get_results($sql);
        if (!empty($items)) {
            $staffs = array_map(function ($item) {
                return $item->staff_id;
            }, $items);
        }

        return $staffs;
    }

    public function getCustomerOrdersReview($customer, $offset = 0, $limit = CUSTOMER_APP_GROUPS_GET_LIMIT)
    {
        global $wpdb;

        $wpTable = $wpdb->prefix . $this->table;
        $booklyStaff = new BooklyStaff();
        $booklyStaffTable = $booklyStaff->getTableName();
        $booklyCustomerApp = new BooklyCustomerAppointment();
        $booklyCustomerAppTable = $booklyCustomerApp->getTableName();
        $appGroupDetail = new AppointmentGroupDetail();
        $appGroupDetailTableName = $appGroupDetail->getTableName();

        $orders = array(
            'need_review' => array(),
            'reviewed' => array(),
        );

        if (empty($customer)) {
            return $orders;
        }

        list($offset, $limit) = $this->validatePaginateData($offset, $limit);

        $approvedStatusInt = $this->convertStatusStringToInt('approved');

        $select = $groupBy = "ag.id, ag.start_date, ag.staff_id, staff.attachment_id, staff.full_name";
        $select .= ", GROUP_CONCAT(agd.appointment_id) AS appointments";

        $from = "(($wpTable AS ag INNER JOIN $booklyStaffTable AS staff ON ag.staff_id = staff.id)";
        $from .= " INNER JOIN $appGroupDetailTableName AS agd ON ag.id = agd.appointment_group_id)";
        $from .= " INNER JOIN $booklyCustomerAppTable AS ca ON agd.appointment_id = ca.appointment_id";

        $where = $wpdb->prepare("ag.customer_id = %d AND ag.status = %d AND ag.start_date < NOW()", $customer, $approvedStatusInt);

        $orderBy = "ag.start_date DESC";

        if ( $offset == 0 ) {
            $needReviewWhere = $where;
            $needReviewWhere .= " AND ca.rating IS NULL";
            $needReviewWhere .= $wpdb->prepare(" AND ag.end_date >= %s", date_create( current_time( 'mysql' ) )->modify( sprintf( '- %s days', get_option( 'bookly_ratings_timeout', 7 ) ) )->format( 'Y-m-d H:i:s' ));

            $sql = "SELECT $select FROM $from WHERE $needReviewWhere GROUP BY $groupBy ORDER BY $orderBy";
            $items = $wpdb->get_results($sql);
            if (!empty($items)) {
                foreach ($items as $item) {
                    $orders['need_review'][] = $this->parseCustomerOrderReview( $item );
                }
            }
        }

        $reviewedWhere = $where;
        $reviewedWhere .= " AND ca.rating IS NOT NULL";

        $sql = "SELECT $select FROM $from WHERE $reviewedWhere GROUP BY $groupBy ORDER BY $orderBy";

        if ($limit > 0) {
            $sql .= $wpdb->prepare(' LIMIT %d, %d', $offset, $limit);
        }

        $items = $wpdb->get_results($sql);
        if (!empty($items)) {
            foreach ($items as $item) {
                $orders['reviewed'][] = $this->parseCustomerOrderReview( $item );
            }
        }

        return $orders;
    }

    public function getStaffOrdersReview($staff, $offset = 0, $limit = CUSTOMER_APP_GROUPS_GET_LIMIT)
    {
        global $wpdb;

        $reviews = array();

        if (empty($staff)) {
            return $reviews;
        }

        list($offset, $limit) = $this->validatePaginateData($offset, $limit);

        $wpTable = $wpdb->prefix . $this->table;
        $booklyCustomer = new BooklyCustomer();
        $booklyCustomerTable = $booklyCustomer->getTableName();
        $booklyCustomerApp = new BooklyCustomerAppointment();
        $booklyCustomerAppTable = $booklyCustomerApp->getTableName();
        $appGroupDetail = new AppointmentGroupDetail();
        $appGroupDetailTableName = $appGroupDetail->getTableName();
        $booklyAppAdapter = Appointment::getInstance();

        $approvedStatusInt = $this->convertStatusStringToInt('approved');

        $select = $groupBy = "ag.id, ag.start_date, ag.customer_id, customer.wp_user_id, customer.full_name";
        $select .= ", GROUP_CONCAT(agd.appointment_id) AS appointments";

        $from = "(($wpTable AS ag INNER JOIN $booklyCustomerTable AS customer ON ag.customer_id = customer.id)";
        $from .= " INNER JOIN $appGroupDetailTableName AS agd ON ag.id = agd.appointment_group_id)";
        $from .= " INNER JOIN $booklyCustomerAppTable AS ca ON agd.appointment_id = ca.appointment_id";

        $where = $wpdb->prepare("staff_id = %d AND ag.status = %d AND ag.start_date < NOW()", $staff, $approvedStatusInt);
        $where .= " AND ca.rating IS NOT NULL";

        $orderBy = "ag.start_date DESC";

        $sql = "SELECT $select FROM $from WHERE $where GROUP BY $groupBy ORDER BY $orderBy";

        if ($limit > 0) {
            $sql .= $wpdb->prepare(' LIMIT %d, %d', $offset, $limit);
        }

        $items = $wpdb->get_results($sql);
        foreach ( $items as $item ) {
            $review = array(
                'date' => parseMysqlDateTime( $item->start_date ),
                'customer' => array(
                    'name' => $item->full_name,
                    'avatar' => '',
                ),
                'services' => array(),
                'rating' => array(),
            );
            if ( !empty( $item->wp_user_id ) ) {
                $userAvatarId = get_user_meta( $item->wp_user_id, 'cutaway_user_avatar', true );
                if ( !empty( $userAvatarId ) ) {
                    $userAvatar = wp_get_attachment_image_src( $userAvatarId );
                    if ( !empty( $userAvatar[0] ) ) {
                        $review['customer']['avatar'] = $userAvatar[0];
                    }
                }
            }

            $apps = $item->appointments;
            $apps = explode( ',', $apps );
            foreach ( $apps as $app ) {
                $appDetail = $booklyAppAdapter->loadAppointmentReviewData( $app );
                $review['services'] = array(
					0 => array(
						'name' => $appDetail['service_name'],
					)
                );
                $review['rating'] = array(
                    'star' => $appDetail['rating'],
                    'percent' => round( ( $appDetail['rating'] / 5 ) * 100, 0 ),
                    'comment' => $appDetail['rating_comment'],
                );
            }

            $reviews[] = $review;
        }

        return $reviews;
    }

    public function makeGroupCustomerAppointments($customer, $staff, $appointments, $numberPeople, $startDate, $endDate)
    {
        global $wpdb;

        $wpTable = $wpdb->prefix . $this->table;

        $return = array(
            'group' => 0,
            'id' => 0,
        );

        if (empty($customer) || empty($staff) || empty($appointments) || empty($numberPeople) || empty($startDate) || empty($endDate)) {
            return $return;
        }

        $this->initTable();

        if (!is_array($appointments)) {
            $appointments = array($appointments);
        }

        $sql = $wpdb->prepare("SELECT MAX(`group`) FROM $wpTable WHERE customer_id = %d", $customer);
        $currentGroup = $wpdb->get_var($sql);
        $nextGroup = empty($currentGroup) ? 1 : $currentGroup + 1;

        $appointmentsStr = maybe_serialize($appointments);

        $result = $wpdb->insert(
            $wpTable,
            array(
                'group' => $nextGroup,
                'customer_id' => $customer,
                'staff_id' => $staff,
                'appointments' => $appointmentsStr,
                'number_people' => $numberPeople,
                'start_date' => $startDate,
                'end_date' => $endDate
            ),
            array('%d', '%d', '%d', '%s', '%d', '%s', '%s')
        );

        if ( $result ) {
            $appGroupDetail = new AppointmentGroupDetail( $wpdb->insert_id );
            $result = $appGroupDetail->insertAppointmentGroupDetail( $appointments );
        }

        return $result ? array(
            'group' => $nextGroup,
            'id' => $wpdb->insert_id,
        ) : $return;
    }

    public function editGroupCustomerAppointments($group, $customer, $dataUpdate = array())
    {
        global $wpdb;

        $wpTable = $wpdb->prefix . $this->table;

        if (empty($group) || empty($customer) || empty($dataUpdate)) {
            return false;
        }

        $this->initTable();

        $newData = $newDataFormat = array();

        foreach ($dataUpdate as $key => $value) {
            if ($key == 'appointments') {
                if (!is_array($value)) {
                    $value = array($value);
                }
                $value = maybe_serialize($value);
            }

            $newData[$key] = $value;
            switch ($key) {
                case 'staff_id':
                case 'status':
                    $newDataFormat[] = '%d';
                    break;
                default:
                    $newDataFormat[] = '%s';
            }
        }

        $result = $wpdb->update(
            $wpTable,
            $newData,
            array(
                'group' => $group,
                'customer_id' => $customer
            ),
            $newDataFormat,
            array('%d', '%d')
        );

        return $result !== false;
    }

    public function deleteAppoinmentGroupByConditions($conditions)
    {
        global $wpdb;

        $wpTable = $wpdb->prefix . $this->table;

        if (empty($conditions)) {
            return false;
        }

        $conditionsFormat = array();
        $conditions = is_array($conditions) ? $conditions : array($conditions);
        foreach (array_keys($conditions) as $attribute) {
            $attributeFormat = $this->getAttributeFormat($attribute);
            if (!empty($attributeFormat)) {
                $conditionsFormat[] = $attributeFormat;
            }
        }

        if (empty($conditionsFormat)) {
            return false;
        }

        $result = $wpdb->delete($wpTable, $conditions, $conditionsFormat);

        return $result !== false;
    }

    public function getListPendingAppointmentsToDelete()
    {
        global $wpdb;

        $wpTable = $wpdb->prefix . $this->table;

        $status = 'pending';
        $statusInt = $this->convertStatusStringToInt($status);

        if (empty($statusInt)) {
            return array();
        }

        $sql = $wpdb->prepare("SELECT id, staff_id, appointments, `start_date` FROM $wpTable WHERE `status` = %d AND TIMESTAMPDIFF(MINUTE, created_date, NOW()) > %d ORDER BY created_date LIMIT 0, 10", $statusInt, 30);
        $items = $wpdb->get_results($sql);
        $apps = array();

        foreach ($items as $item) {
            $staff = $item->staff_id;
            $date = $item->start_date;
            $date = explode(' ', $date);
            $date = str_replace('-', '/', $date[0]);

            $apps[$staff] = !empty($apps[$staff]) ? $apps[$staff] : array();
            $apps[$staff][$date] = !empty($apps[$staff][$date]) ? $apps[$staff][$date] : array();

            $apps[$staff][$date][] = (array) $item;
        }

        return $apps;
    }

    public function getStaffAppointments($staff, $offset = 0, $limit = STAFF_APP_GROUPS_GET_LIMIT)
    {
        global $wpdb;

        $wpTable = $wpdb->prefix . $this->table;

        if (empty($staff)) {
            return array();
        }

        list($offset, $limit) = $this->validatePaginateData($offset, $limit);

        $appointments = array();
        $booklyAppAdapter = Appointment::getInstance();
        $booklyCustomerAdapter = Customer::getInstance();
        $appGroupDetail = new AppointmentGroupDetail();
        $appGroupDetailTableName = $appGroupDetail->getTableName();

        $select = $groupBy = "ag.id, customer_id, `status`, `start_date`";
        $select .= ", GROUP_CONCAT(agd.appointment_id) AS appointments";

        $from = "$wpTable AS ag INNER JOIN $appGroupDetailTableName AS agd ON ag.id = agd.appointment_group_id";

        $sql = $wpdb->prepare("SELECT $select FROM $from WHERE staff_id = %d GROUP BY $groupBy", $staff);
        $sql .= ' ORDER BY `status`, `start_date`';
        if ($limit > 0) {
            $sql .= $wpdb->prepare(' LIMIT %d, %d', $offset, $limit);
        }
        $items = $wpdb->get_results($sql);
        if (!empty($items)) {
            foreach ($items as $item) {
                $status = $item->status;
                $statusStr = $this->convertStatusIntToString( $status );
                $apps = $item->appointments;
                $apps = explode( ',', $apps );
                $customer = $booklyCustomerAdapter->loadCustomerBy( array( 'id' => $item->customer_id ) );
                if (!empty($apps)) {
                    $serviceName = array();
                    foreach ($apps as $app) {
                        $appDetail = $booklyAppAdapter->getAppointmentDetail($app);
                        if (!empty($appDetail)) {
                            $serviceName[] = $appDetail['service_name'];
                        }
                    }

                    if ( !empty( $serviceName ) ) {
                        $appointments[] = array(
                            'id' => $item->id,
                            'customer' => array(
                                'full_name' => $customer->getFullName()
                            ),
                            'services' => array(
                                'name' => implode( ',', $serviceName )
                            ),
                            'date' => $item->start_date,
                            'status' => $statusStr,
                        );
                    }
                }
            }
        }

        return $appointments;
    }

    public function getStaffAppointment($id)
    {
        global $wpdb;

        $wpTable = $wpdb->prefix . $this->table;

        $appointment = array();

        if (empty($id)) {
            return $appointment;
        }

        $wpTable = $wpdb->prefix . $this->table;
        $booklyCustomer = new BooklyCustomer();
        $booklyCustomerTable = $booklyCustomer->getTableName();

        $booklyAppAdapter = Appointment::getInstance();
        $appGroupDetail = new AppointmentGroupDetail();
        $appGroupDetailTableName = $appGroupDetail->getTableName();

        $select = $groupBy = "ag.id, ag.start_date, ag.staff_id, Customer.wp_user_id, Customer.full_name";
        $select .= ", GROUP_CONCAT(agd.appointment_id) AS appointments";

        $from = "($wpTable AS ag INNER JOIN $booklyCustomerTable AS Customer ON ag.customer_id = Customer.id)";
        $from .= " INNER JOIN $appGroupDetailTableName AS agd ON ag.id = agd.appointment_group_id";

        $where = $wpdb->prepare("ag.id = %d", $id);

        $sql = "SELECT $select FROM $from WHERE $where GROUP BY $groupBy";
        $item = $wpdb->get_row($sql);
        if (!empty($item)) {
            $attachmentId = 0;
            if ( !empty( $item->wp_user_id ) ) {
                $customerAvatar = get_user_meta( $item->wp_user_id, 'cutaway_user_avatar', true );
                if ( !empty( $customerAvatar ) ) {
                    $attachmentId = $customerAvatar;
                }
            }
            $apps = $item->appointments;
            $apps = explode( ',', $apps );
            $appGroupMeta = new AppointmentGroupMeta( $item->id );
            if (!empty($apps)) {
                foreach ( $apps as $app ) {
                    $dataReview = $booklyAppAdapter->loadAppointmentReviewData( $app );

                    $appointment = array(
                        'date' => $item->start_date,
                        'customer' => array(
                            'logo' => $attachmentId,
                            'name' => $item->full_name,
                        ),
                        'services' => array(
                            0 => array(
                                'id' => $dataReview['service_id'],
                                'name' => $dataReview['service_name'],
                                'price' => $dataReview['service_price'],
                                'time_process' => $dataReview['service_time'] / 60,
                            ),
                        ),
                        'times_list' => $appGroupMeta->getAppointmentGroupMeta( '_bookly_booked_block_times' ),
                    );
                }
            }
        }

        return $appointment;
    }

    public function refundAppointment( $appGroupId )
    {
        global $wpdb;

        $wpTable = $wpdb->prefix . $this->table;

        $result = false;

        if (empty($appGroupId)) {
            return $result;
        }

        $wpTable = $wpdb->prefix . $this->table;

        $booklyCustomerAppAdapter = CustomerAppointment::getInstance();
        $appGroupDetail = new AppointmentGroupDetail();
        $appGroupDetailTableName = $appGroupDetail->getTableName();

        $select = $groupBy = "ag.id, staff_id, `start_date`";
        $select .= ", GROUP_CONCAT(agd.appointment_id) AS appointments";

        $from = "$wpTable AS ag INNER JOIN $appGroupDetailTableName AS agd ON ag.id = agd.appointment_group_id";

        $where = $wpdb->prepare("ag.id = %d", $appGroupId);

        $sql = "SELECT $select FROM $from WHERE $where GROUP BY $groupBy";
        $item = $wpdb->get_row($sql);
        if (!empty($item)) {
            $staff = $item->staff_id;
            $date = $item->start_date;
            $date = explode( ' ', $date );
            $date = $date[0];
            $date = str_replace( '-', '/', $date );
            $apps = $item->appointments;
            $apps = explode( ',', $apps );
            if (!empty($apps)) {
                foreach ( $apps as $app ) {
                    $result = $booklyCustomerAppAdapter->refundCustomerAppointment( $app );

                    if ( !$result ) {
                        break;
                    }
                }
            }
            if ( $result ) {
                $cancelStatus = $this->convertStatusStringToInt( 'cancelled' );
                $result = $wpdb->update(
                    $wpTable,
                    array(
                        'status' => $cancelStatus,
                    ),
                    array(
                        'id' => $appGroupId,
                    ),
                    array( '%d' ),
                    array( '%d' )
                );

                if ( $result ) {
                    $booklyStaffAdapter = \BooklyAdapter\Entities\Staff::getInstance();
                    $result = $booklyStaffAdapter->updateTimeAvailableOfStaff($staff, $date);
                }
            }
        }

        return $result;
    }

    private function parseCustomerOrderReview( $review )
    {
        $apps = $review->appointments;
        $apps = explode( ',', $apps );
        $appGroupMeta = new AppointmentGroupMeta( $review->id );
        $booklyAppAdapter = Appointment::getInstance();
        $dataOrder = array();

        if (!empty($apps)) {
            foreach ( $apps as $app ) {
                $dataReview = $booklyAppAdapter->loadAppointmentReviewData( $app );

                $dataOrder = array(
                    'token' => $dataReview['token'],
                    'date' => $review->start_date,
                    'barber' => array(
                        'id' => $review->staff_id,
                        'logo' => $review->attachment_id,
                        'name' => $review->full_name,
                    ),
                    'services' => array(
                        0 => array(
                            'id' => $dataReview['service_id'],
                            'name' => $dataReview['service_name'],
                            'price' => $dataReview['service_price'],
                            'time_process' => $dataReview['service_time'] / 60,
                        ),
                    ),
                    'times_list' => $appGroupMeta->getAppointmentGroupMeta( '_bookly_booked_block_times' ),
                );

                if (!is_null($dataReview['rating'])) {
                    $dataOrder['rating'] = !empty($dataReview['rating']) ? $dataReview['rating'] : 0;
                    $dataOrder['comment'] = !empty($dataReview['rating_comment']) ? $dataReview['rating_comment'] : '';
                }
            }
        }

        return $dataOrder;
    }

    private function initTable()
    {
        global $wpdb;

        $wpTable = $wpdb->prefix . $this->table;

        $sqlCheckTableExists = $wpdb->prepare('SHOW TABLES LIKE %s', $wpTable);
        $tableExists = $wpdb->get_var($sqlCheckTableExists);

        if (empty($tableExists)) {
            $sql = "CREATE TABLE IF NOT EXISTS $wpTable(
                `id` INT NOT NULL AUTO_INCREMENT,
                `group` INT NOT NULL,
                `customer_id` INT(10) NOT NULL,
                `staff_id` INT(10) NOT NULL,
                `appointments` TEXT NOT NULL,
                `start_date` DATETIME NOT NULL,
                `end_date` DATETIME NOT NULL,
                `number_people` TINYINT NOT NULL DEFAULT 1,
                `status` TINYINT NOT NULL DEFAULT -1,
                `paypal_status` VARCHAR(255),
                `created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE (`group`, `customer_id`),
                INDEX (`customer_id`)
            ) ENGINE = InnoDB";

            $wpdb->query($sql);
        }
    }

    private function validatePaginateData($offset, $limit)
    {
        $offset = intval($offset);
        if (empty($offset) || $offset < 0) {
            $offset = 0;
        }

        $limit = intval($limit);
        if (empty($limit)) {
            $limit = CUSTOMER_APP_GROUPS_GET_LIMIT;
        }

        return array($offset, $limit);
    }

    private function convertStatusStringToInt($status)
    {
        $intStatus = 1;

        if (empty($status)) {
            return $intStatus;
        }

        foreach ($this->statuses as $int => $string) {
            if ($string == $status) {
                $intStatus = $int;
                break;
            }
        }

        return $intStatus;
    }

    private function convertStatusIntToString($status)
    {
        $strStatus = $this->statuses['1'];

        if (empty($status) || empty($this->statuses[$status])) {
            return $strStatus;
        }

        return $this->statuses[$status];
    }

    private function getAttributeFormat($attribute)
    {
        switch ($attribute) {
            case 'id':
            case 'group':
            case 'customer_id':
            case 'staff_id':
            case 'status':
            case 'number_people':
                return '%d';
            case 'appointments':
            case 'start_date':
            case 'end_date':
            case 'paypal_status':
            case 'created_date':
                return '%s';
        }

        return '';
    }

    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new AppointmentGroup();
        }

        return self::$_instance;
    }
}