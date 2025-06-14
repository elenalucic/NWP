from django.urls import path
from . import views

app_name = "app"

urlpatterns = [

    path('home/', views.home, name='home'),
    path('delete_apartman/<int:apartman_id>/', views.delete_apartman, name='delete_apartman'),
    path('add_apartman/', views.add_apartman, name='add_apartman'),
    path('apartman/<int:apartman_id>/', views.apartman_detail, name='apartman_detail'),
    path('fetch_bookings/<int:apartman_id>/', views.fetch_bookings, name='fetch_bookings'),
    path('add_booking/', views.add_booking, name='add_booking'),
    path('edit_apartman/<int:apartman_id>/', views.edit_apartman, name='edit_apartman'), 




]

