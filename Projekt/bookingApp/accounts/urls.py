from django.urls import path
from . import views

app_name = "accounts"

urlpatterns = [
        path('register/', views.register, name="register"),
        path('register_api/', views.register_api, name='register_api'),
        path('login_api/', views.login_api, name='login_api'),
        path('delete_api/',views.delete_user_api,name='delete_api'),


]