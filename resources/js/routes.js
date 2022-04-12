import App from './components/AppComponent.vue'
import AdminDashboard from './components/Admin/Dashboard.vue'
import AdminAppointments from './components/Appointment/Appointments.vue'
import AddAppointment from './components/Appointment/AddAppointment.vue'
import EditAppointment from './components/Appointment/EditAppointment.vue'



export const routes = [
    {path: '/',component: App},
	{path: '/home',component: App},
    // {path:'/service/dashboard',component: ServiceDashboard,name:'servicedashboard',meta: { auth: true}},
    // {path:'/service/appointments',component: AdminAppointments,name:'serviceappointments',meta: { auth: true}}, 
    // {path:'/service/add-appointment',component: AddAppointment,name:'serviceaddappointment',meta: { auth: true}}, 
    // {path:'/service/multi-appointment',component: MultiAppointment,name:'multiserviceappointment',meta: { auth: true}}, 
    // {path:'/service/add-client',component: CreateClient,name:'servicecreateclient',meta: { auth: true}},
    // {path:'/service/list-clients',component: ListClient,name:'servicelistclient',meta: { auth: true}},
    {path:'/admin/editappointment/:appointmentid',component: EditAppointment,props: true,name:'editappointment',meta: { auth: true}},
    // {path:'/service/editappointment/:appointmentid',component: EditAppointment,props: true,name:'serviceeditappointment',meta: { auth: true}},
    // {path:'/admin/appointment/:appointmentid',component: ViewAppointment,props: true,name:'viewappointment',meta: { auth: true}},
    // {path:'/service/appointment/:appointmentid',component: ViewAppointment,props: true,name:'serviceviewappointment',meta: { auth: true}},
    // {path:'/service/excel-appointments',component: ExcelAppointment,name:'serviceexcelappointments',meta: { auth: true}},
    // {path:'/admin/excel-appointments',component: ExcelAppointment,name:'adminexcelappointments',meta: { auth: true}},
    
    {path:'/admin/dashboard',component: AdminDashboard,name:'admindashboard',meta: { auth: true}},
    {path:'/admin/appointments',component: AdminAppointments,name:'appointments',meta: { auth: true}}, 
    {path:'/admin/add-appointment',component: AddAppointment,name:'addappointment',meta: { auth: true}},
    // {path:'/admin/multi-appointment',component: MultiAppointment,name:'multiappointment',meta: { auth: true}}, 
    // {path:'/admin/add-client',component: CreateClient,name:'createclient',meta: { auth: true}},
    // {path:'/admin/add-executive',component: CreateExecutive,name:'addexecutive',meta: { auth: true}},
    // {path:'/admin/list-executive',component: ListExecutive,name:'listexecutive',meta: { auth: true}},
    // {path:'/admin/add-groomer',component: CreateGroomer,name:'addgroomer',meta: { auth: true}},
    // {path:'/admin/list-groomer',component: ListGroomer,name:'listgroomer',meta: { auth: true}},
    // {path:'/admin/list-clients',component: ListClient,name:'listclient',meta: { auth: true}},
    // {path:'/admin/editclient/:clientid',component: EditClient,props: true,name:'editclient',meta: { auth: true}},
    // {path:'/service/editclient/:clientid',component: EditClient,props: true,name:'serviceeditclient',meta: { auth: true}},
    // {path:'/admin/profile/:clientid',component: Profile,props: true,name:'addpet',meta: { auth: true}},
    // {path:'/service/profile/:clientid',component: Profile,props: true,name:'serviceaddpet',meta: { auth: true}},
    // {path:'/admin/groomer-profile/:clientid',component: GroomerProfile,props: true,name:'groomerprofile',meta: { auth: true}},
    // {path:'/admin/editgroomer/:clientid',component: EditGroomer,props: true,name:'editgroomer',meta: { auth: true}},
    // {path:'/admin/executive-profile/:clientid',component: ExecutiveProfile,props: true,name:'executiveprofile',meta: { auth: true}},
    // {path:'/admin/editexecutive/:clientid',component: EditExecutive,props: true,name:'editexecutive',meta: { auth: true}},
    // {path:'/admin/pet-breed',component: DogBreed,name:'petbreed',meta: { auth: true}},
    // {path:'/admin/classification-month',component: ClassificationMonth,name:'classificationmonth',meta: { auth: true}},
    // {path:'/admin/pet-classification',component: PetClassification,name:'petclassification',meta: { auth: true}},
    // {path:'/admin/service',component: Service,name:'service',meta: { auth: true}},
    // {path:'/admin/pet-category',component: PetCategory,name:'petcategory',meta: { auth: true}},
    // {path:'/admin/pet-aggresive',component: PetAggresive,name:'petaggresive',meta: { auth: true}},
    // {path:'/admin/pet-coat',component: PetCoat,name:'petcoat',meta: { auth: true}},
    // {path:'/admin/pet-class',component: PetClass,name:'petclass',meta: { auth: true}},
    // {path:'/admin/country',component: Country,name:'country',meta: { auth: true}},
    // {path:'/admin/state',component: State,name:'state',meta: { auth: true}},
    // {path:'/admin/city',component: City,name:'city',meta: { auth: true}},
    // {path:'/admin/area',component: Area,name:'area',meta: { auth: true}},
    // {path:'/admin/zone',component: Zone,name:'zone',meta: { auth: true}},
    // {path:'/admin/vehicle',component: Vehicle,name:'vehicle',meta: { auth: true}},
    // {path:'/admin/service-mode',component: ServiceMode,name:'servicemode',meta: { auth: true}},
    // {path:'/admin/service-cost',component: ServiceCost,name:'servicecost',meta: { auth: true}},
    // {path:'/admin/classification-cost',component: ClassificationCharge,name:'classificationcharge',meta: { auth: true}},
    // {path:'/admin/time-slot',component: TimeSlot,name:'timeslot',meta: { auth: true}},

    // {path:'/groomer/dashboard',component: GroomerDashboard,name:'groomerdashboard',meta: { auth: true}},
    // {path:'/groomer/booking-detail/:appointmentid',component: BookingDetails,props: true,name:'bookingdetail',meta: { auth: true}},
    // {path:'/groomer/edit-appointment/:appointmentid',component: EditGroomerAppointment,props: true,name:'groomereditappointment',meta: { auth: true}},

    // {path:'/client/home',component: ClientHome,name:'clienthome',meta: { auth: true}},
    // {path:'/client/Payment',component: ClientPayment,name:'cpayment',meta: { auth: true}},
    // {path:'/client/select',component: ClientSelectpet,name:'cselectpet',meta: { auth: true}},
    // {path:'/client/service',component: ClientSelectservice,name:'cselectservice',meta: { auth: true}},
    // {path:'/client/booking/:appointmentid',component: ClientBooking,name:'cbooking',props: true,meta: { auth: true}},
    // {path:'/client/about',component: ClientAbout,name:'cabout',meta: { auth: true}},

    // {path:'/signup',component: Register,name:'signup'}
    
];