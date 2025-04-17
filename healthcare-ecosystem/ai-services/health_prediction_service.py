import numpy as np
from sklearn.ensemble import RandomForestClassifier
from sklearn.preprocessing import StandardScaler
import joblib
import json
import os
from datetime import datetime, timedelta

class HealthPredictionService:
    def __init__(self):
        self.model = None
        self.scaler = StandardScaler()
        self.model_version = "1.0.0"
        self.load_model()

    def load_model(self):
        try:
            self.model = joblib.load('models/health_prediction_model.joblib')
        except:
            self.model = self._train_initial_model()
            os.makedirs('models', exist_ok=True)
            joblib.dump(self.model, 'models/health_prediction_model.joblib')

    def _train_initial_model(self):
        # Initialize with a basic model
        model = RandomForestClassifier(n_estimators=100, random_state=42)
        return model

    def preprocess_health_data(self, health_data):
        """
        Preprocess health data for prediction
        """
        required_fields = [
            'age', 'gender', 'bmi', 'blood_pressure_systolic', 
            'blood_pressure_diastolic', 'heart_rate', 'temperature',
            'oxygen_saturation'
        ]
        
        # Validate required fields
        for field in required_fields:
            if field not in health_data:
                raise ValueError(f"Missing required field: {field}")

        # Extract features
        features = np.array([[
            health_data['age'],
            1 if health_data['gender'].lower() == 'male' else 0,
            health_data['bmi'],
            health_data['blood_pressure_systolic'],
            health_data['blood_pressure_diastolic'],
            health_data['heart_rate'],
            health_data['temperature'],
            health_data['oxygen_saturation']
        ]])

        # Scale features
        scaled_features = self.scaler.transform(features)
        return scaled_features

    def predict_health_risk(self, health_data):
        """
        Predict health risks based on input data
        """
        try:
            features = self.preprocess_health_data(health_data)
            prediction = self.model.predict_proba(features)[0]
            risk_score = prediction[1]  # Probability of high risk

            result = {
                'prediction_type': 'health_risk',
                'prediction_result': {
                    'risk_score': float(risk_score),
                    'risk_level': self._get_risk_level(risk_score)
                },
                'confidence_score': float(max(prediction)),
                'input_parameters': health_data,
                'model_version': self.model_version,
                'recommendations': self._generate_recommendations(risk_score, health_data),
                'risk_factors': self._identify_risk_factors(health_data),
                'next_checkup_date': self._calculate_next_checkup(risk_score),
                'ai_model_metadata': {
                    'model_type': 'RandomForestClassifier',
                    'features_used': list(health_data.keys()),
                    'timestamp': datetime.now().isoformat()
                }
            }

            return result

        except Exception as e:
            return {
                'error': str(e),
                'status': 'failed'
            }

    def _get_risk_level(self, risk_score):
        if risk_score < 0.3:
            return 'LOW'
        elif risk_score < 0.7:
            return 'MODERATE'
        else:
            return 'HIGH'

    def _generate_recommendations(self, risk_score, health_data):
        recommendations = []
        
        # BMI-based recommendations
        if health_data['bmi'] > 25:
            recommendations.append({
                'category': 'lifestyle',
                'action': 'Consider a balanced diet and regular exercise',
                'priority': 'high' if health_data['bmi'] > 30 else 'medium'
            })

        # Blood pressure recommendations
        if health_data['blood_pressure_systolic'] > 140 or health_data['blood_pressure_diastolic'] > 90:
            recommendations.append({
                'category': 'medical',
                'action': 'Monitor blood pressure regularly and consult healthcare provider',
                'priority': 'high'
            })

        # General recommendations based on risk score
        if risk_score > 0.7:
            recommendations.append({
                'category': 'urgent',
                'action': 'Schedule immediate consultation with healthcare provider',
                'priority': 'high'
            })

        return recommendations

    def _identify_risk_factors(self, health_data):
        risk_factors = []
        
        if health_data['bmi'] > 25:
            risk_factors.append({
                'factor': 'BMI',
                'level': 'high',
                'value': health_data['bmi']
            })

        if health_data['blood_pressure_systolic'] > 140:
            risk_factors.append({
                'factor': 'Blood Pressure',
                'level': 'high',
                'value': f"{health_data['blood_pressure_systolic']}/{health_data['blood_pressure_diastolic']}"
            })

        if health_data['oxygen_saturation'] < 95:
            risk_factors.append({
                'factor': 'Oxygen Saturation',
                'level': 'low',
                'value': health_data['oxygen_saturation']
            })

        return risk_factors

    def _calculate_next_checkup(self, risk_score):
        if risk_score > 0.7:
            return (datetime.now() + timedelta(days=7)).isoformat()
        elif risk_score > 0.3:
            return (datetime.now() + timedelta(days=30)).isoformat()
        else:
            return (datetime.now() + timedelta(days=90)).isoformat()

    def update_model(self, new_training_data):
        """
        Update the model with new training data
        """
        try:
            # Implement model updating logic here
            pass
        except Exception as e:
            return {
                'error': str(e),
                'status': 'failed'
            } 