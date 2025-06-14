from django.shortcuts import render
from django.http import  HttpResponseRedirect,JsonResponse
from django.urls import reverse
from django.contrib.auth.models import User
from app.models import Account
from app.forms import UserForm, AccountForm
from django.contrib.auth import authenticate, login, logout
from rest_framework import status
from rest_framework.decorators import api_view
from rest_framework.response import Response
from .serializers import UserSerializer
from django.contrib.auth.hashers import make_password
from django.views.decorators.csrf import csrf_exempt
from django.contrib.auth.forms import AuthenticationForm



def register(request):
    if request.method == 'POST':
        user_form = UserForm(request.POST)
        account_form = AccountForm(request.POST)
        if user_form.is_valid() and account_form.is_valid():
            user = user_form.save()
            user.set_password(user_form.cleaned_data['password1'])
            user.save()
            account = account_form.save(commit=False)
            account.user = user
            account.save()
            login(request, user)
            return HttpResponseRedirect(reverse('app:home'))
        else:
            return render(request, 'accounts/register.html', {'user_form': user_form, 'account_form': account_form})
    else:
        user_form = UserForm()
        account_form = AccountForm()
        return render(request, 'accounts/register.html', {'user_form': user_form, 'account_form': account_form})
    

@api_view(['POST'])
def register_api(request):
    if request.method == 'POST':
        serializer = UserSerializer(data=request.data)
        if serializer.is_valid():
            password = make_password(request.data.get('password'))
            user = User.objects.create(
                username=request.data.get('username'),
                email=request.data.get('email'),
                password=password
            )
            user.save()  
            return Response(serializer.data, status=status.HTTP_201_CREATED)
        return Response(serializer.errors, status=status.HTTP_400_BAD_REQUEST)

    
@api_view(['GET'])
def register_api(request):
    if request.method == 'GET':
        users = User.objects.all()
        serializer = UserSerializer(users, many=True)
        return Response(serializer.data)

@csrf_exempt
@api_view(['POST'])
def login_api(request):
    if request.method == 'POST':
        form = AuthenticationForm(request, data=request.POST)
        if form.is_valid():
            username = form.cleaned_data.get('username')
            password = form.cleaned_data.get('password')
            user = authenticate(request, username=username, password=password)
            if user is not None:
                login(request, user)
                return JsonResponse({'success': True})
        return JsonResponse({'success': False, 'errors': form.errors}, status=400)
    return JsonResponse({'success': False, 'message': 'Invalid request method.'}, status=400)

@api_view(['POST'])
def logout_api(request):
    logout(request)
    return Response({'message': 'Logged out successfully'}, status=status.HTTP_200_OK)

@csrf_exempt
@api_view(['POST'])
def delete_user_api(request, user_id):
    try:
        user = User.objects.get(pk=user_id)
    except User.DoesNotExist:
        return Response({'error': 'User not found'}, status=status.HTTP_404_NOT_FOUND)
    if request.method == 'DELETE':
        user.delete()
        return Response({'message': 'User deleted successfully'}, status=status.HTTP_204_NO_CONTENT)