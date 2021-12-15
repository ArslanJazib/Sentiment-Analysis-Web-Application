# Sentiment Analyzer.py
#!/usr/bin/python

import os
import sys
import json
import joblib
import itertools
from statistics import mode
import numpy as np
import sklearn
from keras.models import load_model
from sklearn.ensemble import VotingClassifier
from keras.preprocessing.text import Tokenizer
from sklearn.metrics import classification_report
from sklearn.model_selection import train_test_split
from keras.preprocessing.sequence import pad_sequences
from sklearn.feature_extraction.text import CountVectorizer

def sentimentAnalyzer(tweet):
    # Pre-Trained SVM Classifier
    svm_classifier = joblib.load('assets/trained_models/Svm_Classifier_linear.pkl')

    # Vectorizer used for training
    vectorizer = joblib.load('assets/trained_models/SVM_Vectorizer.pkl')

    # Transforming test data using count vectorizer
    svm_naive_tweet=vectorizer.transform([tweet])

    # Predicting test data values
    svm_prediction=svm_classifier.predict(svm_naive_tweet)
    svm_prediction=int(''.join(map(str,svm_prediction.tolist())))

    # Pre-Trained Naive Bayes Classifier
    naive_classifier = joblib.load('assets/trained_models/NaiveBayes_Classifier.pkl')

    # Predicting test data values
    naive_prediction=naive_classifier.predict(svm_naive_tweet)
    naive_prediction=int(''.join(map(str,naive_prediction.tolist())))

    # Pre-Trained LSTM Classifier
    lstm_classifier = load_model('assets/trained_models/NeuralNetworkLSTM.h5')

    # LSTM Tokenizer used for training
    tokenizer = joblib.load('assets/trained_models/lstm_tokenizer.pkl')
    tokenizer.fit_on_texts(tweet)
    lstm_tweet = tokenizer.texts_to_sequences(tweet)
    try:
        lstm_tweet = pad_sequences(lstm_tweet)
        # Predicting test data values
        lstm_prediction=(lstm_classifier.predict(lstm_tweet) > 0.5).astype("int32")
        lstm_prediction = list(itertools.chain(*lstm_prediction))
        lstm_prediction = max(lstm_prediction, key = lstm_prediction.count)
        # Voting on multiple combinations
        print(json.dumps(mode([svm_prediction, naive_prediction, lstm_prediction])))
    except ValueError:
        print(json.dumps(0))





# This file perform sentiment analysis on tweets to be stored in the mySQL database
if __name__ == "__main__":
        tweet =sys.argv[1]
        tweet =json.loads(tweet)
        sentimentAnalyzer(tweet)

